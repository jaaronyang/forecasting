<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Base\DashboardBaseController;

class PPICController extends DashboardBaseController
{
    public function dashboard()
    {
        $dataChart = $this->getDataChart();

        return view('ppic.dashboard', [
            'title' => 'Dashboard PPIC',
            'dataChart' => $dataChart
        ]);
    }
}
