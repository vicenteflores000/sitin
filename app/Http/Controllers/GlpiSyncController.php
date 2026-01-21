<?php

namespace App\Http\Controllers;

use App\Models\GlpiLocation;
use App\Services\GlpiService;
use Illuminate\Http\Request;

class GlpiSyncController extends Controller
{
    public function syncLocations(GlpiService $glpi)
    {
        $locations = $glpi->getLocations();

        foreach ($locations as $location) {
            GlpiLocation::updateOrCreate(
                ['glpi_id' => $location['id']],
                [
                    'name' => $location['name'],
                    'parent_id' => $location['locations_id'] ?? null,
                ]
            );
        }

        return back()->with('success', 'Ubicaciones sincronizadas correctamente');
    }
}
