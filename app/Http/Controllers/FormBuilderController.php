<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Form;

class FormBuilderController extends Controller
{
    public function saveForm(Request $request)
    {
        $formData = $request->input('formData');

        foreach ($formData as $field) {
            $formField = new Form();
            $formField->type = $field['type'];
            $formField->label = $field['label'];

            if (isset($field['values'])) {
                $formField->values = json_encode($field['values']);
            }

            $formField->save();
        }

        return response()->json(['message' => 'Form data saved successfully']);
    }

    public function getFormData()
    {
        $formData = Form::all();

        return response()->json(['formData' => $formData]);
    }
}
