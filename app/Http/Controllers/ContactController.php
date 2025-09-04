<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function index()
    {
        return view('pages.contact');
    }

    /**
     * Handle contact form submission.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'school_name' => 'nullable|string|max:255',
            'inquiry_type' => 'required|string',
            'message' => 'required|string|max:5000',
            'consent' => 'required|accepted',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Prepare contact data
            $contactData = [
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'school_name' => $request->school_name,
                'inquiry_type' => $request->inquiry_type,
                'subject' => 'New Contact Form Submission: ' . ucfirst(str_replace('_', ' ', $request->inquiry_type)),
                'message' => $request->message,
            ];

            // Send email notification to admin
            Mail::send('emails.contact', $contactData, function ($mail) use ($contactData) {
                $mail->to(config('mail.from.address'))
                    ->subject($contactData['subject'])
                    ->replyTo($contactData['email'], $contactData['name']);
            });

            // Send confirmation to user
            Mail::send('emails.contact-confirmation', $contactData, function ($mail) use ($contactData) {
                $mail->to($contactData['email'], $contactData['name'])
                    ->subject('Thank you for contacting EduVault Pro');
            });

            return back()->with('success', 'Thank you for your message! We will get back to you soon.');
        } catch (\Exception $e) {
            Log::error('Contact form error: ' . $e->getMessage());
            return back()
                ->with('error', 'Sorry, there was an error sending your message. Please try again later.')
                ->withInput();
        }
    }
}
