import { readFileSync, writeFileSync, existsSync } from 'node:fs';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const buildRoot = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../../public/build');
const src = existsSync(path.join(buildRoot, '.vite/manifest.json'))
    ? path.join(buildRoot, '.vite/manifest.json')
    : path.join(buildRoot, 'manifest.json');
const out = path.join(buildRoot, 'manifest.json');

const manifest = JSON.parse(readFileSync(src, 'utf8'));

const result = {};
for (const [key, value] of Object.entries(manifest)) {
    result[key] = value;
    if (key.startsWith('src/')) {
        result['resources/panels/' + key] = value;
    }
}

writeFileSync(out, JSON.stringify(result, null, 2));
console.log('Manifest fixed from ' + path.basename(src) + ': added resources/panels/ prefixed entry keys.');
