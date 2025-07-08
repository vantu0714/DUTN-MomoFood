<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipient;

class RecipientController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:15',
            'recipient_address' => 'required|string|max:500',
            'is_default' => 'nullable|boolean',
        ]);

        $userId = Auth::id();

        // Nếu chọn mặc định, bỏ mặc định các địa chỉ khác trước
        if (!empty($validated['is_default'])) {
            Recipient::where('user_id', $userId)->update(['is_default' => false]);
        }

        // Tạo địa chỉ mới
        $recipient = Recipient::create([
            'user_id' => $userId,
            'recipient_name' => $validated['recipient_name'],
            'recipient_phone' => $validated['recipient_phone'],
            'recipient_address' => $validated['recipient_address'],
            'is_default' => $validated['is_default'] ?? false,
        ]);

        return redirect()->route('clients.order')->with('success', 'Thêm địa chỉ mới thành công!');
    }

    public function select(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:recipients,id'
        ]);

        session(['selected_recipient_id' => $request->recipient_id]);

        return back()->with('success', 'Đã chọn địa chỉ thành công.');
    }
}
