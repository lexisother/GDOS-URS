<?php

namespace App\Controllers;

use App\Models\Activity;
use App\Models\Employee;
use App\Models\Entry;
use Illuminate\Http\Request;

class ApiController
{
    public function submit(Request $request)
    {
        if ($request->get('name')) {
            foreach ($request->all() as $key => $_) {
                if (!str_starts_with($key, "min-")) continue;
                $name = str_replace("min-", "", $key);
                $finalName = str_replace("-", " ", $name);

                $minuten = $request->get($key);
                if (!$minuten) continue;

                Entry::create([
                    'medewerker_id' => Employee::where('naam', 'Alyxia Sother')->first()->medewerker_id,
                    'datum' => $request->get('date'),
                    'activiteit_id' => Activity::where('naam', $finalName)->first()->activiteit_id,
                    'minuten' => $minuten,
                ]);

                echo "<script>window.location.href='/';</script>";
            }
        }
    }
}
