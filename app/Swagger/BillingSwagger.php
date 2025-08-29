<?php

namespace App\Swagger;


/**
 *
 *
 * * @OA\Get(
 *     path="/api/billing",
 *     tags={"Billing"},
 *     summary="Fetch patients with billing today",
 *     description="Retrieves all patients with transactions today that have at least one 'Done' service (consultation, laboratory, or medication) but not yet marked as 'Complete'.",
 *     @OA\Response(
 *         response=200,
 *         description="List of patients with active billing",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=5),
 *             @OA\Property(property="firstname", type="string", example="Maria"),
 *             @OA\Property(property="lastname", type="string", example="Santos"),
 *             @OA\Property(property="age", type="integer", example=35),
 *             @OA\Property(property="contact_number", type="string", example="09123456789"),
 *             @OA\Property(property="barangay", type="string", example="San Roque")
 *         ))
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/billing/{transactionId}",
 *     tags={"Billing"},
 *     summary="Get billing details for a transaction",
 *     description="Fetches the billing breakdown for a transaction including consultation, laboratory, and medication details (if completed). Returns total billing with patient information.",
 *     @OA\Parameter(
 *         name="transactionId",
 *         in="path",
 *         required=true,
 *         description="ID of the transaction",
 *         @OA\Schema(type="integer", example=123)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Billing details retrieved successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="patient_id", type="integer", example=5),
 *             @OA\Property(property="transaction_id", type="integer", example=123),
 *             @OA\Property(property="transaction_type", type="string", example="Consultation"),
 *             @OA\Property(property="firstname", type="string", example="Juan"),
 *             @OA\Property(property="lastname", type="string", example="Dela Cruz"),
 *             @OA\Property(property="age", type="integer", example=42),
 *             @OA\Property(property="gender", type="string", example="Male"),
 *             @OA\Property(
 *                 property="address",
 *                 type="object",
 *                 @OA\Property(property="street", type="string", example="Purok 2"),
 *                 @OA\Property(property="barangay", type="string", example="San Isidro")
 *             ),
 *             @OA\Property(property="consultation_amount", type="number", example=200.00),
 *             @OA\Property(property="laboratory_total", type="number", example=450.00),
 *             @OA\Property(property="medication_total", type="number", example=120.00),
 *             @OA\Property(property="total_billing", type="number", example=770.00),
 *             @OA\Property(
 *                 property="laboratories",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="laboratory_type", type="string", example="X-Ray"),
 *                     @OA\Property(property="amount", type="number", example=300.00),
 *                     @OA\Property(property="status", type="string", example="Done")
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="medication",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="item_description", type="string", example="Amoxicillin"),
 *                     @OA\Property(property="quantity", type="integer", example=10),
 *                     @OA\Property(property="unit", type="string", example="Capsule"),
 *                     @OA\Property(property="amount", type="number", example=120.00)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction not found"
 *     )
 * )
 */

class  BillingSwagger {}
