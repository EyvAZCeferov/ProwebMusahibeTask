<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'amount' => (float) $this->amount,
            'notes' => $this->notes,
            'transaction_date' => $this->created_at->format('Y-m-d H:i:s'),

            'status' => $this->when(
                $this->relationLoaded('transaction_status') && $this->transaction_status,
                new TransactionStatusResource($this->transaction_status)
            ),

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],

            'account' => [
                'id' => $this->account->id,
                'code' => $this->account->code,
                'currency' => $this->account->currency->code,
            ],

            'dispensed_banknotes' => $this->when(
                $this->relationLoaded('transaction_details') && $this->transaction_details->isNotEmpty(),
                function () {
                    return $this->transaction_details->map(function ($detail) {
                        return [
                            'denomination' => $detail->atmBanknote->name,
                            'quantity' => $detail->quantity,
                        ];
                    });
                }
            ),

            'counterparty_account_id' => $this->when(
                !is_null($this->additional_data) && isset($this->additional_data['counterparty_account_id']),
                fn() => $this->additional_data['counterparty_account_id']
            ),

            'created_at' => $this->created_at->format("d.m.Y H:i")
        ];
    }
}
