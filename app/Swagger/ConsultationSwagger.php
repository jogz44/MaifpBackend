<?php

namespace App\Swagger;

/**
 *
 * * @OA\Get(
 *     path="/api/new_consultations/return",
 *     tags={"Consultations"},
 *     summary="Fetch patients with returned consultations (today only)",
 *     description="This endpoint retrieves patients whose consultations have a status of 'Returned' for the current date,
 *                  along with their related transactions, vitals, consultation, and laboratories.",
 *     @OA\Response(
 *         response=200,
 *         description="List of patients with returned consultations",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=12),
 *                 @OA\Property(property="firstname", type="string", example="Juan"),
 *                 @OA\Property(property="lastname", type="string", example="Dela Cruz"),
 *                 @OA\Property(
 *                     property="transaction",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=101),
 *                         @OA\Property(property="transaction_date", type="string", format="date", example="2025-08-29"),
 *                         @OA\Property(
 *                             property="consultation",
 *                             type="object",
 *                             @OA\Property(property="status", type="string", example="Returned")
 *                         ),
 *                         @OA\Property(
 *                             property="laboratories",
 *                             type="array",
 *                             @OA\Items(
 *                                 type="object",
 *                                 @OA\Property(property="id", type="integer", example=88),
 *                                 @OA\Property(property="status", type="string", example="Pending")
 *                             )
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch returned consultations"
 *     )
 * )
 *
 *
 * @OA\Post(
 *     path="/api/new_consultations",
 *     tags={"Consultations"},
 *     summary="Store or update a consultation and sync status with laboratories",
 *     description="Creates a new consultation record or updates it if the transaction_id already exists. Automatically updates amount if status is 'Done' and syncs laboratory statuses based on consultation status.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             required={"transaction_id","status"},
 *             @OA\Property(property="transaction_id", type="integer", example=101, description="ID of the related transaction"),
 *             @OA\Property(property="status", type="string", example="Done", description="Status of the consultation"),
 *             @OA\Property(property="amount", type="number", format="float", example=500, description="Amount for the consultation (auto-set if status is Done)"),
 *             @OA\Property(property="remarks", type="string", example="Patient referred for medication", description="Additional notes or remarks")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Consultation successfully created or updated",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Successfully Saved"),
 *             @OA\Property(
 *                 property="consultation",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=55),
 *                 @OA\Property(property="transaction_id", type="integer", example=101),
 *                 @OA\Property(property="status", type="string", example="Done"),
 *                 @OA\Property(property="amount", type="number", example=500)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="The transaction_id field is required.")
 *         )
 *     )
 * )
 */

class ConsultationSwagger {}
