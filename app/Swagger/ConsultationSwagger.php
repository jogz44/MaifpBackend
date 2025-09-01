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
 *
 *
 * * @OA\Post(
 *     path="/api/doctor/store",
 *     tags={"Library Doctor"},
 *     summary="Create a doctor fee",
 *     description="Creates a new doctor fee entry in the library.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="doctor_amount", type="number", format="float", example=500.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Doctor fee created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="successfully create"),
 *             @OA\Property(property="doctor_fee", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/doctor/update/{id}",
 *     tags={"Library Doctor"},
 *     summary="Update a doctor fee",
 *     description="Updates an existing doctor fee entry.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the doctor fee",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="doctor_amount", type="number", format="float", example=750.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Doctor fee updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="successfully update"),
 *             @OA\Property(property="doctor_fee", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Doctor fee not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/doctor/delete/{id}",
 *     tags={"Library Doctor"},
 *     summary="Delete a doctor fee",
 *     description="Deletes a doctor fee entry from the library.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the doctor fee",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Doctor fee deleted successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="successfully delete"),
 *             @OA\Property(property="doctor_fee", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Doctor fee not found"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/doctor",
 *     tags={"Library Doctor"},
 *     summary="Get all doctor fees",
 *     description="Fetches a list of all doctor fees from the library.",
 *     @OA\Response(
 *         response=200,
 *         description="List of doctor fees",
 *         @OA\JsonContent(type="array", @OA\Items(type="object"))
 *     )
 * )
 */

class ConsultationSwagger {}
