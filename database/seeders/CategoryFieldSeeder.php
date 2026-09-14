<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceType;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryFieldSeeder extends Seeder
{
    public function run(): void
    {
        $fieldSets = [
            'mechanics-auto-repair' => [
                ['key' => 'vehicle_types', 'label' => 'Vehicle Types You Work On', 'type' => 'multiselect', 'required' => true, 'options' => ['Bike', 'Scooter', 'Car', 'Auto / Rickshaw', 'Truck', 'Bus', 'Tractor', 'Other']],
                ['key' => 'specialties', 'label' => 'Specialisations', 'type' => 'text', 'placeholder' => 'e.g. Engine, Gearbox, Electrical, Body work'],
                ['key' => 'on_site_service', 'label' => 'I provide on-site service at the customer\'s location', 'type' => 'checkbox'],
                ['key' => 'highway_service', 'label' => 'I provide highway / roadside emergency service', 'type' => 'checkbox', 'required' => true],
                ['key' => 'emergency_service', 'label' => 'Available for emergency / urgent callouts 24x7', 'type' => 'checkbox'],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
                ['key' => 'service_radius_km', 'label' => 'Service Radius (km) — how far you travel', 'type' => 'number', 'required' => true],
                ['key' => 'working_hours', 'label' => 'Working Hours', 'type' => 'text', 'placeholder' => 'e.g. 9 AM – 9 PM, Mon–Sat'],
            ],

            'towing-roadside-assistance' => [
                ['key' => 'vehicle_types', 'label' => 'Vehicles You Tow', 'type' => 'multiselect', 'required' => true, 'options' => ['Bike', 'Car / SUV', 'Truck', 'Bus', 'Other']],
                ['key' => 'towing_types', 'label' => 'Towing Equipment', 'type' => 'multiselect', 'required' => true, 'options' => ['Flatbed', 'Hook & Chain', 'Wheel-lift', 'Winch']],
                ['key' => 'available_24x7', 'label' => 'Available 24x7 for emergency towing', 'type' => 'checkbox', 'required' => true],
                ['key' => 'highway_service', 'label' => 'I cover highways / expressways', 'type' => 'checkbox', 'required' => true],
                ['key' => 'service_radius_km', 'label' => 'Service Radius (km)', 'type' => 'number', 'required' => true],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number'],
                ['key' => 'tools_equipment', 'label' => 'Additional Equipment / Notes', 'type' => 'textarea', 'placeholder' => 'Jump start kit, tyre tools, straps...'],
            ],

            'ac-repair-services' => [
                ['key' => 'ac_types', 'label' => 'AC Types You Service', 'type' => 'multiselect', 'required' => true, 'options' => ['Split AC', 'Window AC', 'Cassette AC', 'Tower AC', 'Central / Ducted AC', 'Inverter AC']],
                ['key' => 'services_offered', 'label' => 'Services You Offer', 'type' => 'multiselect', 'required' => true, 'options' => ['Installation', 'Repair', 'Servicing & Cleaning', 'Gas Refilling / Top-up', 'AMC Contract']],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
                ['key' => 'on_site_service', 'label' => 'I provide on-site service at the customer\'s location', 'type' => 'checkbox'],
                ['key' => 'emergency_service', 'label' => 'Available for emergency / urgent repair', 'type' => 'checkbox'],
                ['key' => 'service_radius_km', 'label' => 'Service Radius (km)', 'type' => 'number'],
                ['key' => 'working_hours', 'label' => 'Working Hours', 'type' => 'text', 'placeholder' => 'e.g. 10 AM – 8 PM, all 7 days'],
            ],

            'plumbing' => [
                ['key' => 'plumbing_services', 'label' => 'Services You Offer', 'type' => 'multiselect', 'required' => true, 'options' => ['Leak Repair', 'Tap / Mixer Fitting', 'Pipe Installation & Replacement', 'Bathroom Renovation', 'Water Heater / Geyser Installation', 'Drain & Blockage Cleaning']],
                ['key' => 'emergency_service', 'label' => 'Available for emergency / urgent plumbing callouts', 'type' => 'checkbox'],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
                ['key' => 'on_site_service', 'label' => 'I provide on-site service at the customer\'s location', 'type' => 'checkbox'],
                ['key' => 'service_radius_km', 'label' => 'Service Radius (km)', 'type' => 'number'],
                ['key' => 'tools_equipment', 'label' => 'Tools / Equipment you carry', 'type' => 'textarea'],
            ],

            'electrical-services' => [
                ['key' => 'electrical_services', 'label' => 'Services You Offer', 'type' => 'multiselect', 'required' => true, 'options' => ['House Wiring', 'Fan & Light Installation', 'Switch / Socket / MCB Repair', 'Inverter & Battery Setup', '3-Phase & Industrial Work', 'Electrical Audit']],
                ['key' => 'licensed_electrician', 'label' => 'I hold a licensed electrician certification', 'type' => 'checkbox', 'required' => true],
                ['key' => 'emergency_service', 'label' => 'Available for emergency electrical callouts', 'type' => 'checkbox'],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
                ['key' => 'on_site_service', 'label' => 'I provide on-site service at the customer\'s location', 'type' => 'checkbox'],
                ['key' => 'working_hours', 'label' => 'Working Hours', 'type' => 'text', 'placeholder' => 'e.g. 9 AM – 8 PM, Su–Fr'],
            ],

            'carpentry-furniture' => [
                ['key' => 'carpentry_services', 'label' => 'Services You Offer', 'type' => 'multiselect', 'required' => true, 'options' => ['Furniture Repair', 'Custom Furniture Making', 'Kitchen / Wardrobe Installation', 'Door & Window Repair', 'Modular Furniture', 'Wood Polishing']],
                ['key' => 'custom_designs', 'label' => 'I take custom / made-to-order work', 'type' => 'checkbox'],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
                ['key' => 'on_site_service', 'label' => 'I provide on-site service at the customer\'s location', 'type' => 'checkbox'],
                ['key' => 'service_radius_km', 'label' => 'Service Radius (km)', 'type' => 'number'],
                ['key' => 'tools_equipment', 'label' => 'Tools / Equipment you carry', 'type' => 'textarea'],
            ],

            'painting-renovation' => [
                ['key' => 'painting_services', 'label' => 'Services You Offer', 'type' => 'multiselect', 'required' => true, 'options' => ['Interior Painting', 'Exterior Painting', 'Waterproofing / Damp Proofing', 'Texture & Designer Finishes', 'Wallpaper Installation', 'Wood / Metal Painting']],
                ['key' => 'team_size', 'label' => 'Team Size', 'type' => 'number', 'placeholder' => 'Number of workers (including you)'],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
                ['key' => 'on_site_service', 'label' => 'I bring all equipment & paint up to sample/shade cards', 'type' => 'checkbox'],
                ['key' => 'working_hours', 'label' => 'Working Hours', 'type' => 'text', 'placeholder' => 'e.g. 8 AM – 6 PM, all days'],
            ],

            'masonry-construction' => [
                ['key' => 'masonry_services', 'label' => 'Services You Offer', 'type' => 'multiselect', 'required' => true, 'options' => ['House Construction', 'Plastering & Masonry', 'Flooring & Tiling', 'Brick / Block Work', 'Waterproofing', 'Renovation & Demolition']],
                ['key' => 'team_size', 'label' => 'Team Size', 'type' => 'number', 'placeholder' => 'Number of masons & helpers'],
                ['key' => 'heavy_machinery', 'label' => 'I can arrange heavy machinery (JCB, concrete mixer)', 'type' => 'checkbox'],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
                ['key' => 'on_site_service', 'label' => 'I work on-site / at the project location', 'type' => 'checkbox'],
            ],

            'home-cleaning-pest-control' => [
                ['key' => 'cleaning_areas', 'label' => 'Areas / Services You Cover', 'type' => 'multiselect', 'required' => true, 'options' => ['Home Cleaning', 'Office Cleaning', 'Kitchen Deep Clean', 'Bathroom Deep Clean', 'Sofa & Carpet Cleaning', 'Window Cleaning']],
                ['key' => 'pest_control_types', 'label' => 'Pest Control (if you provide it)', 'type' => 'multiselect', 'options' => ['Rodents', 'Cockroaches', 'Bed Bugs', 'Termites', 'Mosquitoes', 'Birds']],
                ['key' => 'eco_friendly', 'label' => 'I use eco-friendly / non-toxic products', 'type' => 'checkbox'],
                ['key' => 'team_size', 'label' => 'Team Size', 'type' => 'number'],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
                ['key' => 'on_site_service', 'label' => 'I provide on-site service at the customer\'s location', 'type' => 'checkbox'],
            ],

            'moving-packing' => [
                ['key' => 'truck_types', 'label' => 'Vehicle / Truck Size', 'type' => 'multiselect', 'required' => true, 'options' => ['Small (pickup)', 'Medium (Tata Ace / Bolero)', 'Large (Container / Trailer)']],
                ['key' => 'team_size', 'label' => 'Team Size', 'type' => 'number', 'placeholder' => 'Number of movers'],
                ['key' => 'long_distance', 'label' => 'I provide long-distance / inter-city moves', 'type' => 'checkbox', 'required' => true],
                ['key' => 'packing_material', 'label' => 'I provide packing material & boxes', 'type' => 'checkbox'],
                ['key' => 'insurance_cover', 'label' => 'I offer careful-handling / transit guarantee', 'type' => 'checkbox'],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number'],
            ],

            'gardening-landscaping' => [
                ['key' => 'gardening_services', 'label' => 'Services You Offer', 'type' => 'multiselect', 'required' => true, 'options' => ['Lawn Mowing & Trimming', 'Tree Trimming & Pruning', 'Landscaping & Garden Design', 'Irrigation / Sprinkler Setup', 'Garden Cleaning & Waste Removal']],
                ['key' => 'equipment_owned', 'label' => 'I bring all tools & equipment', 'type' => 'checkbox'],
                ['key' => 'team_size', 'label' => 'Team Size', 'type' => 'number'],
                ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
                ['key' => 'on_site_service', 'label' => 'I provide on-site service at the customer\'s location', 'type' => 'checkbox'],
            ],
        ];

        $defaultSet = [
            ['key' => 'experience_years', 'label' => 'Years of Experience', 'type' => 'number', 'required' => true],
            ['key' => 'working_hours', 'label' => 'Working Hours', 'type' => 'text', 'placeholder' => 'e.g. 9 AM – 9 PM, Mon–Sat'],
            ['key' => 'service_radius_km', 'label' => 'Service Radius (km) — how far you travel', 'type' => 'number', 'required' => true],
            ['key' => 'on_site_service', 'label' => 'I provide on-site service at the customer\'s location', 'type' => 'checkbox'],
            ['key' => 'emergency_service', 'label' => 'Available for emergency / urgent callouts', 'type' => 'checkbox'],
            ['key' => 'tools_equipment', 'label' => 'Tools / Equipment you carry', 'type' => 'textarea'],
        ];

        $newCategories = [
            ['name' => 'Mechanics & Auto Repair', 'slug' => 'mechanics-auto-repair', 'icon' => 'fas fa-car-side', 'category_type' => 'field'],
            ['name' => 'AC Repair & Installation', 'slug' => 'ac-repair-services', 'icon' => 'fas fa-snowflake', 'category_type' => 'field'],
            ['name' => 'Towing & Roadside Assistance', 'slug' => 'towing-roadside-assistance', 'icon' => 'fas fa-truck-loading', 'category_type' => 'field'],
        ];

        $newServiceTypes = [
            'mechanics-auto-repair' => ['Bike Repair & Service', 'Car Repair & Service', 'Truck / Heavy Vehicle Repair', 'Engine & Transmission Work', 'Car AC & Electrical Repair', 'Emergency Roadside Mechanic'],
            'ac-repair-services' => ['Split AC Repair & Service', 'Window AC Repair & Service', 'AC Installation', 'AC Gas Refilling / Top-up', 'Central / Ducted AC Service', 'AC AMC Contract'],
            'towing-roadside-assistance' => ['Bike Towing', 'Car / SUV Towing', 'Truck Towing', 'Flatbed Towing', 'Jump Start / Battery Service', 'Tyre Puncture Help'],
        ];

        foreach ($newCategories as $category) {
            Category::firstOrCreate([
                'slug' => $category['slug'],
            ], $category + ['status' => true]);
        }

        foreach ($newServiceTypes as $slug => $types) {
            $category = Category::where('slug', $slug)->first();
            if (! $category) {
                continue;
            }
            foreach ($types as $name) {
                ServiceType::firstOrCreate([
                    'slug' => Str::slug($name),
                ], [
                    'category_id' => $category->id,
                    'name' => $name,
                    'is_active' => true,
                ]);
            }
        }

        // Non-technical categories must be "field" type.
        $fieldSlugs = array_keys($fieldSets);

        foreach (Category::all() as $category) {
            if (in_array($category->slug, $fieldSlugs, true)) {
                $category->update(['category_type' => 'field']);
            } else {
                $category->update(['category_type' => 'technical']);
            }
        }

        foreach ($fieldSets as $slug => $fields) {
            $category = Category::where('slug', $slug)->first();
            if (! $category) {
                continue;
            }
            $category->update(['category_type' => 'field', 'form_fields' => $fields]);
        }

        // Any field-type category without a custom schema gets the default set.
        foreach (Category::where('category_type', 'field')->get() as $category) {
            if (empty($category->form_fields)) {
                $category->update(['form_fields' => $defaultSet]);
            }
        }
    }
}