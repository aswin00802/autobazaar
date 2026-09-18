<?php

namespace App\Services;

use App\Models\Vehicle\VehicleEnquiry;
use App\Models\Vehicle\VehicleModel;
use App\Models\Vehicle\VehicleReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Leads and reviews from the vehicle detail page.
 *
 * The website (fetch from the lead modal) and the mobile app (JSON API) both
 * land here so the validation rules and the stored row are identical. The
 * controllers only decide how to answer — JSON, redirect, or the API envelope.
 */
class VehicleLeadService
{
    public const SOURCES = ['enquiry', 'quotation', 'test_drive', 'loan'];

    public const TIME_SLOTS = ['10:00-12:00', '12:00-14:00', '14:00-16:00', '16:00-18:00'];

    public const SUCCESS_MESSAGES = [
        'enquiry' => 'Thanks! Our team will call you shortly.',
        'quotation' => 'Thanks! Your free quotation is on its way — we will call to confirm.',
        'test_drive' => 'Test drive booked! We will confirm your slot by phone.',
        'loan' => 'Loan enquiry received. A finance executive will call you with your eligibility.',
    ];

    public const REVIEW_MESSAGE = 'Thanks — your review will appear after approval.';

    /* ------------------------------------------------------------- rules */

    public function leadRules(): array
    {
        return [
            'source' => 'required|in:' . implode(',', self::SOURCES),
            'name' => 'required|string|min:2|max:80',
            'mobile' => ['required', 'regex:/^[6-9][0-9]{9}$/'],
            'email' => 'nullable|email|max:120',
            'city' => 'nullable|string|max:80',
            'pincode' => ['nullable', 'regex:/^[1-9][0-9]{5}$/'],
            'vehicle_variant_id' => 'nullable|integer',
            'preferred_at' => 'required_if:source,test_drive|nullable|date|after_or_equal:today',
            'time_slot' => 'required_if:source,test_drive|nullable|in:' . implode(',', self::TIME_SLOTS),
            'loan_amount' => 'required_if:source,loan|nullable|numeric|min:1000|max:10000000',
            'message' => 'nullable|string|max:1000',
            'website' => 'nullable|max:0',   // honeypot — bots fill it, people never see it
        ];
    }

    public function leadMessages(): array
    {
        return [
            'mobile.regex' => 'Enter a valid 10-digit Indian mobile number.',
            'pincode.regex' => 'Enter a valid 6-digit pincode.',
            'preferred_at.required_if' => 'Pick a date for your test drive.',
            'preferred_at.after_or_equal' => 'The test drive date cannot be in the past.',
            'time_slot.required_if' => 'Pick a time slot.',
            'loan_amount.required_if' => 'Tell us the loan amount you need.',
            'website.max' => 'Submission rejected.',
        ];
    }

    public function reviewRules(): array
    {
        return [
            'name' => 'required|string|min:2|max:80',
            'city' => 'nullable|string|max:80',
            'mobile' => ['nullable', 'regex:/^[6-9][0-9]{9}$/'],
            'rating' => 'required|integer|min:1|max:5',
            'title' => 'nullable|string|max:120',
            'body' => 'required|string|min:10|max:2000',
            'website' => 'nullable|max:0',
        ];
    }

    public function reviewMessages(): array
    {
        return [
            'mobile.regex' => 'Enter a valid 10-digit Indian mobile number.',
            'body.min' => 'Tell us a little more — at least 10 characters.',
            'website.max' => 'Submission rejected.',
        ];
    }

    /* ------------------------------------------------------------- store */

    public function storeLead(VehicleModel $model, array $data, Request $request): VehicleEnquiry
    {
        // Only accept a variant that belongs to this model; anything else is dropped.
        $variantId = null;
        if (! empty($data['vehicle_variant_id'])) {
            $variantId = $model->variants()->where('id', (int) $data['vehicle_variant_id'])->value('id');
        }

        $source = $data['source'];

        return DB::transaction(function () use ($model, $data, $request, $variantId, $source) {
            $lead = new VehicleEnquiry();
            $lead->enquiry_no = 'VE-TMP-' . strtoupper(Str::random(10));   // replaced once the id is known
            $lead->vehicle_model_id = $model->id;
            $lead->vehicle_variant_id = $variantId;
            $lead->user_id = auth()->check() ? auth()->id() : null;
            $lead->name = trim($data['name']);
            $lead->mobile = $data['mobile'];
            $lead->email = $data['email'] ?? null;
            $lead->city = $data['city'] ?? null;
            $lead->pincode = $data['pincode'] ?? null;
            $lead->source = $source;
            $lead->preferred_at = $source === 'test_drive' && ! empty($data['preferred_at']) ? $data['preferred_at'] : null;
            $lead->time_slot = $source === 'test_drive' ? ($data['time_slot'] ?? null) : null;
            $lead->loan_amount = $source === 'loan' && isset($data['loan_amount']) ? (float) $data['loan_amount'] : null;
            $lead->message = $data['message'] ?? null;
            $lead->lead_status = 'new';
            $lead->page_url = $request->headers->get('referer') ?: $request->fullUrl();
            $lead->status_id = 1;
            $lead->ip_address = $request->ip();
            $lead->save();

            $lead->enquiry_no = self::enquiryNo($lead->id);
            $lead->saveQuietly();

            return $lead;
        });
    }

    public function storeReview(VehicleModel $model, array $data, Request $request): VehicleReview
    {
        $review = new VehicleReview();
        $review->vehicle_model_id = $model->id;
        $review->user_id = auth()->check() ? auth()->id() : null;
        $review->name = trim($data['name']);
        $review->city = $data['city'] ?? null;
        $review->mobile = $data['mobile'] ?? null;
        $review->rating = (int) $data['rating'];
        $review->title = $data['title'] ?? null;
        $review->body = $data['body'];
        $review->is_verified = 0;
        $review->review_status = 'pending';       // shows only after admin approval
        $review->status_id = 1;
        $review->ip_address = $request->ip();
        $review->save();

        return $review;
    }

    /** VE-0001, VE-0002 … (grows past four digits naturally). */
    public static function enquiryNo(int $id): string
    {
        return 'VE-' . str_pad((string) $id, 4, '0', STR_PAD_LEFT);
    }

    public function successMessage(string $source): string
    {
        return self::SUCCESS_MESSAGES[$source] ?? self::SUCCESS_MESSAGES['enquiry'];
    }
}
