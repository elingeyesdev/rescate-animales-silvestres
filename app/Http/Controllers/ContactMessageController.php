<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ContactMessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(ContactMessageRequest $request): RedirectResponse
    {
        ContactMessage::create([
            'user_id' => Auth::id(),
            'motivo' => $request->input('motivo'),
            'mensaje' => $request->input('mensaje'),
        ]);

        return redirect()->route('profile.index')
            ->with('success', 'Tu mensaje ha sido enviado. Un administrador o encargado se pondrá en contacto contigo pronto.');
    }

    public function update(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        if (!Auth::user()->hasAnyRole(['admin', 'encargado'])) {
            abort(403);
        }

        // Guardar usando query directa
        \Illuminate\Support\Facades\DB::table('contact_messages')
            ->where('id', $contactMessage->id)
            ->update([
                'leido' => true,
                'leido_at' => now(),
                'leido_por' => Auth::id(),
            ]);

        return redirect()->back()->with('success', 'Mensaje marcado como leído.');
    }
}
