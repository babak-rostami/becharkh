<?php

namespace App\Services\Survey;

class SurveyService
{
    public function addSurveyTo($object, $request)
    {
        if ($request->has_survey == 1) {
            $object->surop1 = $request->surop1;
            $object->surop1_count = '0-0';
            $object->surop2 = $request->surop2;
            $object->surop2_count = '0-0';
            if ($request->surop3 && trim($request->surop3) != "") {
                $object->surop3 = $request->surop3;
                $object->surop3_count = '0-0';
            }
            if ($request->surop4 && trim($request->surop3) != "") {
                $object->surop4 = $request->surop4;
                $object->surop4_count = '0-0';
            }
        }
    }
}
