<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'invoice_no'   => $this->invoice_no,
            'from_location'=> $this->from_location,
            'to_location'  => $this->to_location,
            'km'           => $this->total_km,
            'time'         => $this->total_time,
            'km_amount'    => $this->km_amount,
            'margin_amount'=> $this->margin_amount,
            'fuel_amount'  => $this->fuel_amount,
            'wages_amount' => $this->wages_amount,
            'friction_amount'=> $this->friction_amount,
            'tips_amount'  => $this->tips_amount,
            'total_amount' => $this->total_amount,
            'date'         => $this->date
        ];
    }
}
