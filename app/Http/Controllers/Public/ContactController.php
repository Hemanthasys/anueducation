<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function index()
    {
        return view('public.contact.index', [
            'recaptchaSiteKey' => config('services.recaptcha.site_key'),
        ]);
    }

    public function submit(Request $request)
    {
        // Validate form fields
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|max:255',
            'subject'              => 'required|string|max:255',
            'message'              => 'required|string|max:2000',
            'g-recaptcha-response' => 'required',
        ]);

        // Verify reCAPTCHA with Google
        $recaptcha = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => config('services.recaptcha.secret_key'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        if (!$recaptcha->json('success')) {
            return back()
                ->withInput()
                ->withErrors(['recaptcha' => 'reCAPTCHA verification failed. Please try again.']);
        }

        // Save message to database
        $contactMessage = ContactMessage::create([
            'name'       => $validated['name'],
            'email'      => $validated['email'],
            'subject'    => $validated['subject'],
            'message'    => $validated['message'],
            'ip_address' => $request->ip(),
            'status'     => 'new',
        ]);

        // Send email notification to admin
        $adminEmail = SiteSetting::where('key', 'email')->value('value');
        if ($adminEmail) {
            Mail::raw(
                "New contact message from {$validated['name']} ({$validated['email']})\n\n" .
                "Subject: {$validated['subject']}\n\n" .
                "Message:\n{$validated['message']}\n\n" .
                "View in admin: " . url('/admin/contact-messages/' . $contactMessage->id),
                function ($mail) use ($adminEmail, $validated) {
                    $mail->to($adminEmail)
                         ->subject('New Contact Message: ' . $validated['subject']);
                }
            );
        }

        return back()->with('success', __('contact_success'));
    }
}