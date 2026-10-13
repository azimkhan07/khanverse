<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use ZipArchive;

class LocationApiController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Countries
    |--------------------------------------------------------------------------
    */

    public function countries(Request $request): JsonResponse
    {
        $query = Country::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%$q%")
                    ->orWhere('iso2', 'like', "%$q%")
                    ->orWhere('iso3', 'like', "%$q%")
                    ->orWhere('phone_code', 'like', "%$q%");
            });
        }

        if ($request->boolean('all')) {
            return response()->json(['data' => $query->orderBy('name')->get()]);
        }

        return response()->json($query->orderBy('name')->paginate($request->get('per_page', 15)));
    }

    public function storeCountry(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'iso2' => 'nullable|string|max:2',
            'iso3' => 'nullable|string|max:3',
            'phone_code' => 'nullable|string|max:10',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'emoji' => 'nullable|string|max:20',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $validated['status'] ?? true;
        $country = Country::create($validated);

        return response()->json(['message' => 'Country created successfully.', 'country' => $country], 201);
    }

    public function updateCountry(Request $request, $id): JsonResponse
    {
        $country = Country::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'iso2' => 'nullable|string|max:2',
            'iso3' => 'nullable|string|max:3',
            'phone_code' => 'nullable|string|max:10',
            'currency' => 'nullable|string|max:10',
            'currency_symbol' => 'nullable|string|max:10',
            'timezone' => 'nullable|string|max:100',
            'emoji' => 'nullable|string|max:20',
            'status' => 'nullable|boolean',
        ]);

        $country->update($validated);

        return response()->json(['message' => 'Country updated successfully.', 'country' => $country->fresh()]);
    }

    public function destroyCountry($id): JsonResponse
    {
        $country = Country::findOrFail($id);
        $country->delete();

        return response()->json(['message' => 'Country deleted successfully.']);
    }

    /*
    |--------------------------------------------------------------------------
    | States
    |--------------------------------------------------------------------------
    */

    public function states(Request $request): JsonResponse
    {
        $query = State::query()->with('country');

        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->boolean('all')) {
            return response()->json(['data' => $query->orderBy('name')->get()]);
        }

        return response()->json($query->orderBy('name')->paginate($request->get('per_page', 15)));
    }

    public function storeState(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'state_code' => 'nullable|string|max:20',
            'status' => 'nullable|boolean',
        ]);

        $validated['status'] = $validated['status'] ?? true;
        $state = State::create($validated);

        return response()->json(['message' => 'State created successfully.', 'state' => $state->load('country')], 201);
    }

    public function updateState(Request $request, $id): JsonResponse
    {
        $state = State::findOrFail($id);
        $validated = $request->validate([
            'country_id' => 'sometimes|required|exists:countries,id',
            'name' => 'sometimes|required|string|max:255',
            'state_code' => 'nullable|string|max:20',
            'status' => 'nullable|boolean',
        ]);

        $state->update($validated);

        return response()->json(['message' => 'State updated successfully.', 'state' => $state->fresh('country')]);
    }

    public function destroyState($id): JsonResponse
    {
        $state = State::findOrFail($id);
        $state->delete();

        return response()->json(['message' => 'State deleted successfully.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Cities
    |--------------------------------------------------------------------------
    */

    public function cities(Request $request): JsonResponse
    {
        $query = City::query()->with(['country', 'state']);

        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }
        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        if ($request->boolean('all')) {
            return response()->json(['data' => $query->orderBy('name')->get()]);
        }

        return response()->json($query->orderBy('name')->paginate($request->get('per_page', 15)));
    }

    public function storeCity(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'name' => 'required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        if (empty($validated['country_id'])) {
            $validated['country_id'] = State::find($validated['state_id'])?->country_id;
        }
        $validated['status'] = $validated['status'] ?? true;

        $city = City::create($validated);

        return response()->json(['message' => 'City created successfully.', 'city' => $city->load(['country', 'state'])], 201);
    }

    public function updateCity(Request $request, $id): JsonResponse
    {
        $city = City::findOrFail($id);
        $validated = $request->validate([
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'sometimes|required|exists:states,id',
            'name' => 'sometimes|required|string|max:255',
            'status' => 'nullable|boolean',
        ]);

        if (array_key_exists('state_id', $validated) && empty($validated['country_id'])) {
            $validated['country_id'] = State::find($validated['state_id'])?->country_id;
        }

        $city->update($validated);

        return response()->json(['message' => 'City updated successfully.', 'city' => $city->fresh(['country', 'state'])]);
    }

    public function destroyCity($id): JsonResponse
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json(['message' => 'City deleted successfully.']);
    }

    /*
    |--------------------------------------------------------------------------
    | Bulk import (flexible columns: only the columns present are used)
    |--------------------------------------------------------------------------
    */

    public function import(Request $request): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:countries,states,cities',
            'file' => 'required|file|max:20480',
        ]);

        $type = $request->type;
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());

        try {
            $rows = $this->readRows($file->getRealPath(), $ext);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        if (empty($rows)) {
            return response()->json(['message' => 'The uploaded file contains no data rows.'], 422);
        }

        $result = match ($type) {
            'countries' => $this->importCountries($rows),
            'states' => $this->importStates($rows),
            'cities' => $this->importCities($rows),
        };

        return response()->json([
            'message' => sprintf(
                '%s import complete: %d created, %d updated, %d skipped.',
                ucfirst($type),
                $result['created'],
                $result['updated'],
                $result['skipped']
            ),
            'result' => $result,
        ]);
    }

    protected function importCountries(array $rows): array
    {
        $counts = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];

        DB::transaction(function () use ($rows, &$counts) {
            foreach ($rows as $i => $row) {
                $data = $this->pick($row, [
                    'name' => ['name', 'country', 'country_name'],
                    'iso2' => ['iso2', 'iso', 'iso_2', 'code', 'country_code', 'alpha2'],
                    'iso3' => ['iso3', 'iso_3', 'alpha3'],
                    'phone_code' => ['phone_code', 'phone', 'dial_code', 'dial', 'calling_code'],
                    'currency' => ['currency', 'currency_code'],
                    'currency_symbol' => ['currency_symbol', 'symbol'],
                    'timezone' => ['timezone', 'time_zone'],
                    'emoji' => ['emoji', 'flag'],
                    'status' => ['status', 'active', 'is_active'],
                ]);

                if (empty($data['name'])) {
                    $counts['skipped']++;
                    continue;
                }

                $match = null;
                if (!empty($data['iso2'])) {
                    $match = Country::where('iso2', strtoupper($data['iso2']))->first();
                }
                $match = $match ?: Country::where('name', $data['name'])->first();

                if (isset($data['status'])) {
                    $data['status'] = $this->toBool($data['status']);
                }

                if ($match) {
                    $match->update($data);
                    $counts['updated']++;
                } else {
                    Country::create($data);
                    $counts['created']++;
                }
            }
        });

        return $counts;
    }

    protected function importStates(array $rows): array
    {
        $counts = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
        $countries = $this->countryLookup();

        DB::transaction(function () use ($rows, &$counts, $countries) {
            foreach ($rows as $row) {
                $data = $this->pick($row, [
                    'country' => ['country', 'country_name', 'country_code', 'country_iso2'],
                    'name' => ['name', 'state', 'state_name'],
                    'state_code' => ['state_code', 'code', 'iso2', 'iso_3166_2'],
                    'status' => ['status', 'active', 'is_active'],
                ]);

                if (empty($data['name']) || empty($data['country'])) {
                    $counts['skipped']++;
                    continue;
                }

                $countryId = $this->resolveCountry($countries, $data['country']);
                if (!$countryId) {
                    $counts['skipped']++;
                    continue;
                }

                $payload = ['country_id' => $countryId, 'name' => $data['name']];
                if (!empty($data['state_code'])) {
                    $payload['state_code'] = $data['state_code'];
                }
                if (isset($data['status'])) {
                    $payload['status'] = $this->toBool($data['status']);
                }

                $match = State::where('country_id', $countryId)->where('name', $data['name'])->first();
                if ($match) {
                    $match->update($payload);
                    $counts['updated']++;
                } else {
                    State::create($payload);
                    $counts['created']++;
                }
            }
        });

        return $counts;
    }

    protected function importCities(array $rows): array
    {
        $counts = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => []];
        $countries = $this->countryLookup();

        DB::transaction(function () use ($rows, &$counts, $countries) {
            foreach ($rows as $row) {
                $data = $this->pick($row, [
                    'country' => ['country', 'country_name', 'country_code', 'country_iso2'],
                    'state' => ['state', 'state_name', 'state_code', 'region'],
                    'name' => ['name', 'city', 'city_name'],
                    'status' => ['status', 'active', 'is_active'],
                ]);

                if (empty($data['name']) || empty($data['state'])) {
                    $counts['skipped']++;
                    continue;
                }

                $countryId = !empty($data['country']) ? $this->resolveCountry($countries, $data['country']) : null;

                $stateQuery = State::query()->where('name', $data['state']);
                if ($countryId) {
                    $stateQuery->where('country_id', $countryId);
                } else {
                    $stateQuery->orWhere('state_code', $data['state']);
                }
                $state = $stateQuery->first();

                if (!$state) {
                    $counts['skipped']++;
                    continue;
                }

                $payload = [
                    'country_id' => $countryId ?: $state->country_id,
                    'state_id' => $state->id,
                    'name' => $data['name'],
                ];
                if (isset($data['status'])) {
                    $payload['status'] = $this->toBool($data['status']);
                }

                $match = City::where('state_id', $state->id)->where('name', $data['name'])->first();
                if ($match) {
                    $match->update($payload);
                    $counts['updated']++;
                } else {
                    City::create($payload);
                    $counts['created']++;
                }
            }
        });

        return $counts;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Read a CSV or XLSX file into an array of associative rows keyed by
     * normalised header names.
     */
    protected function readRows(string $path, string $ext): array
    {
        $matrix = match ($ext) {
            'csv', 'txt' => $this->readCsv($path),
            'xlsx' => $this->readXlsx($path),
            default => throw new \RuntimeException('Unsupported file type. Please upload a .csv or .xlsx file.'),
        };

        if (count($matrix) < 2) {
            return [];
        }

        $header = array_map(fn ($h) => $this->normalizeKey((string) $h), array_shift($matrix));
        $rows = [];
        foreach ($matrix as $line) {
            if (!is_array($line)) {
                continue;
            }
            // skip fully-empty rows
            if (count(array_filter($line, fn ($v) => $v !== null && $v !== '')) === 0) {
                continue;
            }
            $row = [];
            foreach ($header as $idx => $key) {
                if ($key === '') {
                    continue;
                }
                $row[$key] = isset($line[$idx]) ? trim((string) $line[$idx]) : '';
            }
            $rows[] = $row;
        }

        return $rows;
    }

    protected function readCsv(string $path): array
    {
        $matrix = [];
        if (($handle = fopen($path, 'r')) !== false) {
            // Detect delimiter (comma, semicolon or tab)
            $firstLine = fgets($handle);
            rewind($handle);
            $delimiter = ',';
            if ($firstLine !== false) {
                $counts = [',' => substr_count($firstLine, ','), ';' => substr_count($firstLine, ';'), "\t" => substr_count($firstLine, "\t")];
                arsort($counts);
                $delimiter = array_key_first($counts);
            }
            while (($line = fgetcsv($handle, 0, $delimiter)) !== false) {
                $matrix[] = $line;
            }
            fclose($handle);
        }

        return $matrix;
    }

    protected function readXlsx(string $path): array
    {
        if (!class_exists(ZipArchive::class)) {
            throw new \RuntimeException('Excel (.xlsx) import needs the PHP "zip" extension, which is not enabled. Please save your file as CSV and upload that instead.');
        }

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('Could not open the Excel file.');
        }

        $shared = [];
        if (($sst = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
            $xml = simplexml_load_string($sst);
            if ($xml !== false) {
                foreach ($xml->si as $si) {
                    $text = '';
                    if (isset($si->t)) {
                        $text = (string) $si->t;
                    } else {
                        foreach ($si->r as $r) {
                            $text .= (string) $r->t;
                        }
                    }
                    $shared[] = $text;
                }
            }
        }

        $sheet = $zip->getFromName('xl/worksheets/sheet1.xml');
        if ($sheet === false) {
            foreach (range(2, 5) as $n) {
                $sheet = $zip->getFromName("xl/worksheets/sheet{$n}.xml");
                if ($sheet !== false) {
                    break;
                }
            }
        }
        $zip->close();

        if ($sheet === false) {
            throw new \RuntimeException('Could not read the Excel worksheet.');
        }

        $xml = simplexml_load_string($sheet);
        if ($xml === false) {
            throw new \RuntimeException('Invalid Excel worksheet data.');
        }

        $matrix = [];
        foreach ($xml->sheetData->row as $row) {
            $cells = [];
            foreach ($row->c as $c) {
                $ref = (string) $c['r'];
                $col = $this->columnIndex(preg_replace('/[0-9]+/', '', $ref));
                $type = (string) $c['t'];
                $value = isset($c->v) ? (string) $c->v : '';
                if ($type === 's') {
                    $value = $shared[(int) $value] ?? '';
                } elseif ($type === 'inlineStr') {
                    $value = isset($c->is->t) ? (string) $c->is->t : '';
                }
                $cells[$col] = $value;
            }
            if (empty($cells)) {
                $matrix[] = [];
                continue;
            }
            $max = max(array_keys($cells));
            $line = [];
            for ($i = 0; $i <= $max; $i++) {
                $line[$i] = $cells[$i] ?? '';
            }
            $matrix[] = $line;
        }

        return $matrix;
    }

    protected function columnIndex(string $letters): int
    {
        $letters = strtoupper($letters);
        $num = 0;
        for ($i = 0; $i < strlen($letters); $i++) {
            $num = $num * 26 + (ord($letters[$i]) - 64);
        }

        return $num - 1;
    }

    protected function normalizeKey(string $key): string
    {
        $key = strtolower(trim($key));
        $key = str_replace([' ', '-', '.', '/'], '_', $key);

        return preg_replace('/_+/', '_', $key);
    }

    /**
     * Pick the first present value from the row using the given aliases.
     */
    protected function pick(array $row, array $map): array
    {
        $out = [];
        foreach ($map as $field => $aliases) {
            foreach ($aliases as $alias) {
                if (array_key_exists($alias, $row) && $row[$alias] !== '') {
                    $out[$field] = $row[$alias];
                    break;
                }
            }
        }

        return $out;
    }

    protected function countryLookup(): array
    {
        $map = [];
        foreach (Country::query()->get(['id', 'name', 'iso2', 'iso3']) as $c) {
            if ($c->name) {
                $map[strtolower(trim($c->name))] = $c->id;
            }
            if ($c->iso2) {
                $map[strtoupper(trim($c->iso2))] = $c->id;
            }
            if ($c->iso3) {
                $map[strtoupper(trim($c->iso3))] = $c->id;
            }
        }

        return $map;
    }

    protected function resolveCountry(array $lookup, string $value): ?int
    {
        $value = trim($value);

        return $lookup[$value] ?? $lookup[strtolower($value)] ?? $lookup[strtoupper($value)] ?? null;
    }

    protected function toBool($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        return in_array(strtolower(trim((string) $value)), ['1', 'true', 'yes', 'y', 'active', 'enabled'], true);
    }
}
