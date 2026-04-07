<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RecipientTemplate;
use App\Models\DescriptionTemplate;
use Illuminate\Http\Request;

class TemplateController extends Controller
{
    public function recipientTemplates(Request $request)
    {
        $templates = RecipientTemplate::where('user_id', $request->user()->id)->get();

        return response()->json(['templates' => $templates]);
    }

    public function descriptionTemplates(Request $request)
    {
        $templates = DescriptionTemplate::where('user_id', $request->user()->id)->get();

        return response()->json(['templates' => $templates]);
    }
}
