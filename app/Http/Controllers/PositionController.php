<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Position;

class PositionController extends Controller
{
    private function validatePosition(Request $request, $positionId = null)
    {
        $uniqueRule = $positionId ? 'unique:positions,name,' . $positionId : 'unique:positions,name';
        return $request->validate([
            'name' => 'required|string|max:255|' . $uniqueRule,
            'shop_name' => 'nullable|string|max:255',
            'location' => 'nullable|string',
            'description' => 'nullable|string',
            'duties' => 'nullable|string',
            'salary' => 'nullable|string|max:255',
            'extra_pay' => 'nullable|string|max:255',
            'working_hours' => 'nullable|string|max:255',
            'days_off' => 'nullable|string|max:255',
            'benefits' => 'nullable|string',
            'qualifications' => 'nullable|string',
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatePosition($request);
        $data['is_active'] = true;
        Position::create($data);
        return back()->with('success', 'เพิ่มตำแหน่งงานสำเร็จ');
    }

    public function update(Request $request, Position $position)
    {
        // When updating only 'is_active' (from the main dashboard card toggle), 
        // the form only sends 'name' and 'is_active'. To not overwrite existing fields
        // with nulls, we only update what's provided, or we handle it gracefully.
        // Actually, if it's the toggle form, it only has 'name' (hidden) and 'is_active' (hidden).
        // Let's check if the request intentionally contains full data.
        if ($request->has('is_full_update')) {
            $data = $this->validatePosition($request, $position->id);
            $data['is_active'] = $request->has('is_active') ? true : false;
            $position->update($data);
        } else {
            // It's a simple status toggle or name update from the small card
            $request->validate(['name' => 'required|string|max:255|unique:positions,name,' . $position->id]);
            $position->update([
                'name' => $request->name,
                'is_active' => $request->has('is_active') ? true : false
            ]);
        }

        return back()->with('success', 'แก้ไขตำแหน่งงานสำเร็จ');
    }

    public function destroy(Position $position)
    {
        $position->delete();
        return back()->with('success', 'ลบตำแหน่งงานสำเร็จ');
    }
}
