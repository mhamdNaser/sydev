<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gesture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GestureController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'character' => 'required|string|max:255',
            'start_time' => 'required|integer',
            'end_time' => 'required|integer',
            'duration_ms' => 'required|integer',
            'frame_count' => 'required|integer',
            'frames' => 'required|array|min:1',
        ]);

        DB::beginTransaction();
        try {
            $gesture = Gesture::create([
                'character' => $request->character,
                'user_id' => $request->user_id ?? null,
                'device_id' => $request->device_id ?? null,
                'start_time' => date('Y-m-d H:i:s', $request->start_time / 1000),
                'end_time' => date('Y-m-d H:i:s', $request->end_time / 1000),
                'duration_ms' => $request->duration_ms,
                'frame_count' => $request->frame_count,
                'notes' => $request->notes ?? null,
            ]);

            foreach ($request->frames as $frameData) {
                $frame = $gesture->frames()->create([
                    'frame_id' => $frameData['frame_id'],
                    'timestamp' => $frameData['ts'],
                    'points_count' => count($frameData['points']),
                    'raw_payload' => $frameData,
                ]);

                foreach ($frameData['points'] as $index => $pt) {
                    // حساب الضغط تقريبياً
                    $pressure = 0.0;
                    if (isset($frameData['points'][$index - 1])) {
                        $prev = $frameData['points'][$index - 1];
                        $dx = $pt['x'] - $prev['x'];
                        $dy = $pt['y'] - $prev['y'];
                        $distance = sqrt($dx * $dx + $dy * $dy);
                        $pressure = max(0.0, 1.0 - $distance); // كلما المسافة أصغر → ضغط أعلى
                    } else {
                        $pressure = 1.0; // أول نقطة في الإطار
                    }

                    $frame->points()->create([
                        'point_id' => $pt['id'],
                        'x' => $pt['x'],
                        'y' => $pt['y'],
                        'state' => $pt['state'],
                        'pressure' => $pressure,
                    ]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Gesture saved successfully', 'gesture_id' => $gesture->id], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}


// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Models\Gesture;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\DB;

// class GestureController extends Controller
// {
//    public function store(Request $request)
//     {
//         $request->validate([
//             'character' => 'required|string|max:255',
//             'start_time' => 'required|integer',
//             'end_time' => 'required|integer',
//             'duration_ms' => 'required|integer',
//             'frame_count' => 'required|integer',
//             'frames' => 'required|array|min:1',
//         ]);

//         DB::beginTransaction();
//         try {
//             $gesture = Gesture::create([
//                 'character' => $request->character,
//                 'user_id' => $request->user_id ?? null,
//                 'device_id' => $request->device_id ?? null,
//                 'start_time' => date('Y-m-d H:i:s', $request->start_time / 1000),
//                 'end_time' => date('Y-m-d H:i:s', $request->end_time / 1000),
//                 'duration_ms' => $request->duration_ms,
//                 'frame_count' => $request->frame_count,
//                 'notes' => $request->notes ?? null,
//             ]);

//             foreach ($request->frames as $frameData) {
//                 $frame = $gesture->frames()->create([
//                     'frame_id' => $frameData['frame_id'],
//                     'timestamp' => $frameData['ts'],
//                     'points_count' => count($frameData['points']),
//                     'raw_payload' => $frameData,
//                 ]);

//                 foreach ($frameData['points'] as $pt) {
//                     $frame->points()->create([
//                         'point_id' => $pt['id'],
//                         'x' => $pt['x'],
//                         'y' => $pt['y'],
//                         'state' => $pt['state'],
//                         'pressure' => $pt['pressure'] ?? null,
//                     ]);
//                 }
//             }

//             DB::commit();
//             return response()->json(['message' => 'Gesture saved successfully', 'gesture_id' => $gesture->id], 201);
//         } catch (\Exception $e) {
//             DB::rollBack();
//             return response()->json(['error' => $e->getMessage()], 500);
//         }
//     }
// }
