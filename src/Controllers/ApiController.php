<?php

namespace App\Controllers;

use App\Models\Entry;
use Illuminate\Database\Capsule\Manager;
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
                    'medewerker_id' => Manager::table('medewerker')->where('naam', $request->get('name'))->get()[0]->medewerker_id,
                    'datum' => $request->get('date'),
                    'activiteit_id' => Manager::table('activiteit')->where('naam', $finalName)->get()[0]->activiteit_id,
                    'minuten' => $minuten,
                ]);

                echo "<script>window.location.href='/';</script>";
            }
        }
    }
}
