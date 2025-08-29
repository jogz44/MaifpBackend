<?php

namespace App\Swagger;


/**
 * @OA\Get(
 *     path="/api/guarantee",
 *     tags={"Guarantee Letters"},
 *     summary="Fetch patients eligible for a guarantee letter",
 *     description="Fetches patients with completed transactions today that do not yet have a funded guarantee letter.",
 *     @OA\Response(
 *         response=200,
 *         description="List of eligible patients",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="firstname", type="string", example="Juan"),
 *                 @OA\Property(property="lastname", type="string", example="Dela Cruz"),
 *                 @OA\Property(property="middlename", type="string", example="Santos"),
 *                 @OA\Property(property="ext", type="string", example="Jr."),
 *                 @OA\Property(property="birthdate", type="string", format="date", example="1995-03-15"),
 *                 @OA\Property(property="age", type="integer", example=28),
 *                 @OA\Property(property="contact_number", type="string", example="09171234567"),
 *                 @OA\Property(property="barangay", type="string", example="San Isidro")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/guarantee/store",
 *     tags={"Guarantee Letters"},
 *     summary="Create a new guarantee letter",
 *     description="Stores a guarantee letter if funds are available, deducting billing from the remaining budget.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"patient_id", "transaction_id", "medication_total", "total_billing"},
 *             @OA\Property(property="patient_id", type="integer", example=1, description="ID of the patient"),
 *             @OA\Property(property="transaction_id", type="integer", example=10, description="ID of the related transaction"),
 *             @OA\Property(property="consultation_amount", type="number", example=500, description="Total consultation amount"),
 *             @OA\Property(property="laboratory_total", type="number", example=1500, description="Total lab charges"),
 *             @OA\Property(property="medication_total", type="number", example=800, description="Total medication charges"),
 *             @OA\Property(property="total_billing", type="number", example=2800, description="Grand total billing")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Guarantee letter created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Billing created successfully"),
 *             @OA\Property(property="billing", type="object"),
 *             @OA\Property(property="total_funds", type="number", example=10000),
 *             @OA\Property(property="remaining_funds", type="number", example=7500)
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Not enough funds",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Not enough funds. Please add more funds before creating this billing. Remaining funds: 1500")
 *         )
 *     )
 * )
 */


class  GuaranteeLetterSwagger {}
