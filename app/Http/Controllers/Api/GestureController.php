<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gesture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GestureController extends Controller
{
    public function index()
    {
        try {
            // نجلب الإيماءات مع الفريمات والنقاط المرتبطة بكل فريم
            $gestures = Gesture::with(['frames.points'])->get();

            $formattedGestures = $gestures->map(function ($gesture) {
                $points = collect();

                foreach ($gesture->frames as $frame) {
                    foreach ($frame->points as $pt) {
                        $points->push([
                            'x' => $pt->x,
                            'y' => $pt->y,
                            'dx' => $pt->dx,
                            'dy' => $pt->dy,
                            'vx' => $pt->vx,
                            'vy' => $pt->vy,
                            'angle' => $pt->angle,
                            'pressure' => $pt->pressure,
                            'state' => $pt->state,
                            'timestamp' => $frame->timestamp,
                            'delta_ms' => $frame->delta_ms,
                            'frame_id' => $frame->frame_id,
                        ]);
                    }
                }

                // ترتيب النقاط حسب الزمن
                $points = $points->sortBy('timestamp')->values();

                return [
                    'id' => $gesture->id,
                    'character' => $gesture->character,
                    'duration_ms' => $gesture->duration_ms,
                    'frame_count' => $gesture->frame_count,
                    'points_count' => $points->count(),
                    'points' => $points,
                ];
            });

            return response()->json([
                'count' => $formattedGestures->count(),
                'data' => $formattedGestures,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch gestures',
                'details' => $e->getMessage(),
            ], 500);
        }
    }


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
            // إنشاء Gesture
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
                // إنشاء Frame مع raw_payload بالكامل
                $frame = $gesture->frames()->create([
                    'frame_id' => $frameData['frame_id'],
                    'timestamp' => $frameData['ts'],
                    'points_count' => count($frameData['points']),
                    'delta_ms' => $frameData['delta_ms'],
                ]);

                // إنشاء نقاط Point مباشرة من raw_payload
                foreach ($frameData['points'] as $pt) {
                    $frame->points()->create([
                        'point_id' => $pt['id'],
                        'x' => $pt['x'],
                        'y' => $pt['y'],
                        'dx' => $pt['dx'],
                        'dy' => $pt['dy'],
                        'vx' => $pt['vx'],
                        'vy' => $pt['vy'],
                        'angle' => $pt['angle'],
                        'state' => $pt['state'] ?? 'move',
                        'pressure' => $pt['pressure'] ?? 1.0,
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'message' => 'Gesture saved successfully',
                'gesture_id' => $gesture->id
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function countByCharacter($character)
    {
        try {
            // عدّ جميع الإشارات التي تحمل نفس اسم الحرف
            $count = Gesture::where('character', $character)->count();

            return response()->json([
                'character' => $character,
                'count' => $count
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to count gestures',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'character' => 'required|string|max:255',
    //         'start_time' => 'required|integer',
    //         'end_time' => 'required|integer',
    //         'duration_ms' => 'required|integer',
    //         'frame_count' => 'required|integer',
    //         'frames' => 'required|array|min:1',
    //     ]);

    //     DB::beginTransaction();
    //     try {
    //         // إنشاء gesture
    //         $gesture = Gesture::create([
    //             'character' => $request->character,
    //             'user_id' => $request->user_id ?? null,
    //             'device_id' => $request->device_id ?? null,
    //             'start_time' => date('Y-m-d H:i:s', $request->start_time / 1000),
    //             'end_time' => date('Y-m-d H:i:s', $request->end_time / 1000),
    //             'duration_ms' => $request->duration_ms,
    //             'frame_count' => $request->frame_count,
    //             'notes' => $request->notes ?? null,
    //         ]);

    //         foreach ($request->frames as $frameData) {
    //             $frame = $gesture->frames()->create([
    //                 'frame_id' => $frameData['frame_id'],
    //                 'timestamp' => $frameData['ts'],
    //                 'points_count' => count($frameData['points']),
    //                 'raw_payload' => $frameData,
    //             ]);

    //             $windowSize = 3; // عدد النقاط لتنعيم الضغط
    //             $pointBuffer = [];

    //             foreach ($frameData['points'] as $index => $pt) {
    //                 // إضافة النقطة للمصفوفة المؤقتة
    //                 $pointBuffer[] = $pt;
    //                 if (count($pointBuffer) > $windowSize) {
    //                     array_shift($pointBuffer); // إزالة النقطة الأقدم
    //                 }

    //                 $pressure = 1.0; // افتراضي للنقطة الأولى
    //                 if (count($pointBuffer) > 1) {
    //                     $sumSpeed = 0.0;
    //                     for ($i = 1; $i < count($pointBuffer); $i++) {
    //                         $prev = $pointBuffer[$i - 1];
    //                         $curr = $pointBuffer[$i];

    //                         $dx = $curr['x'] - $prev['x'];
    //                         $dy = $curr['y'] - $prev['y'];
    //                         $distance = sqrt($dx * $dx + $dy * $dy);

    //                         $dt = max(1, ($curr['ts'] ?? $frameData['ts']) - ($prev['ts'] ?? $frameData['ts']));
    //                         $speed = $distance / $dt;
    //                         $sumSpeed += $speed;
    //                     }

    //                     $avgSpeed = $sumSpeed / (count($pointBuffer) - 1);
    //                     $pressure = max(0.1, min(1.0, 1.0 - $avgSpeed * 5)); // تعديل عامل الحساسية حسب الحاجة
    //                 }

    //                 $frame->points()->create([
    //                     'point_id' => $pt['id'],
    //                     'x' => $pt['x'],
    //                     'y' => $pt['y'],
    //                     'state' => $pt['state'],
    //                     'pressure' => $pressure,
    //                 ]);
    //             }
    //         }

    //         DB::commit();
    //         return response()->json([
    //             'message' => 'Gesture saved successfully',
    //             'gesture_id' => $gesture->id
    //         ], 201);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
}
