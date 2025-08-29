<?php

namespace App\Swagger;

/** * @OA\Get(
 *     path="/api/transactions",
 *     tags={"Transactions"},
 *     summary="Get all transaction",
 *     @OA\Response(
 *         response=200,
 *         description="List of transaction"
 *     )
 * )
 *
 * /** * @OA\Get(
 *     path="/api/transactions/qualified",
 *     tags={"Transactions"},
 *     summary="Fetch all the patients are qualified for consultation",
 *     @OA\Response(
 *         response=200,
 *         description="List of patients qualified"
 *     )
 * )
 *

 * * @OA\Get(
 *     path="/api/transactions/{id}",
 *     tags={"Transactions"},
 *     summary="Get a transaction by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Transaction ID",
 *         @OA\Schema(type="integer", example=10)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction details fetched successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=10),
 *             @OA\Property(property="status", type="string", example="Qualified"),
 *             @OA\Property(property="transaction_type", type="string", example="Consultation"),
 *             @OA\Property(property="transaction_date", type="string", format="date", example="2025-08-29"),
 *             @OA\Property(
 *                 property="vital",
 *                 type="object",
 *                 nullable=true,
 *                 @OA\Property(property="id", type="integer", example=22),
 *                 @OA\Property(property="blood_pressure", type="string", example="120/80"),
 *                 @OA\Property(property="pulse_rate", type="string", example="75 bpm")
 *             ),
 *             @OA\Property(
 *                 property="laboratories",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=33),
 *                     @OA\Property(property="laboratory_type", type="string", example="Blood Test"),
 *                     @OA\Property(property="status", type="string", example="Pending")
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="representative",
 *                 type="object",
 *                 nullable=true,
 *                 @OA\Property(property="id", type="integer", example=7),
 *                 @OA\Property(property="name", type="string", example="John Doe"),
 *                 @OA\Property(property="relation", type="string", example="Father"),
 *                 @OA\Property(property="contact_number", type="string", example="+639123456789")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions/add",
 *     summary="Add a new transaction and vitals for an existing patient",
 *     tags={"Transactions"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"patient_id","rep_name","transaction_type","transaction_date","transaction_mode","purpose","height","weight"},
 *             @OA\Property(property="patient_id", type="integer", example=108),
 *             @OA\Property(property="rep_name", type="string", example="Jane Doe"),
 *             @OA\Property(property="rep_relationship", type="string", example="Mother"),
 *             @OA\Property(property="rep_contact", type="string", example="09123456789"),
 *             @OA\Property(property="rep_barangay", type="string", example="Central"),
 *             @OA\Property(property="rep_address", type="string", example="Blk 5 Lot 9"),
 *             @OA\Property(property="rep_purok", type="string", example="Purok 2"),
 *             @OA\Property(property="rep_street", type="string", example="Rizal St."),
 *             @OA\Property(property="rep_province", type="string", example="Davao del Norte"),
 *             @OA\Property(property="rep_city", type="string", example="Tagum City"),
 *
 *             @OA\Property(property="transaction_type", type="string", example="Consultation"),
 *             @OA\Property(property="transaction_date", type="string", format="date", example="2025-08-29"),
 *             @OA\Property(property="transaction_mode", type="string", example="Walk-in"),
 *             @OA\Property(property="purpose", type="string", example="General check-up"),
 *
 *             @OA\Property(property="height", type="string", example="170 cm"),
 *             @OA\Property(property="weight", type="string", example="65 kg"),
 *             @OA\Property(property="bmi", type="string", example="22.5"),
 *             @OA\Property(property="temperature", type="string", example="36.7"),
 *             @OA\Property(property="waist", type="string", example="32"),
 *             @OA\Property(property="pulse_rate", type="string", example="80"),
 *             @OA\Property(property="sp02", type="string", example="98%"),
 *             @OA\Property(property="heart_rate", type="string", example="75"),
 *             @OA\Property(property="blood_pressure", type="string", example="120/80"),
 *             @OA\Property(property="respiratory_rate", type="string", example="18"),
 *             @OA\Property(property="medicine", type="string", example="Paracetamol"),
 *             @OA\Property(property="LMP", type="string", example="2025-08-01"),
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction and vitals added successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Transaction and vitals added successfully for existing patient."),
 *             @OA\Property(property="transaction_number", type="string", example="2025-08-29-00108"),
 *             @OA\Property(property="patient", type="object"),
 *             @OA\Property(property="transaction", type="object"),
 *             @OA\Property(property="vital", type="object"),
 *             @OA\Property(property="representative", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Patient not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Unexpected error"
 *     )
 * )
 *
 *  * @OA\Put(
 *     path="/api/transactions/update/{id}",
 *     tags={"Transactions"},
 *     summary="Update a transaction",
 *     description="Updates the details of a transaction by its ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the transaction to update",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="transaction_type", type="string", example="Consultation"),
 *             @OA\Property(property="transaction_mode", type="string", example="Walk-in"),
 *             @OA\Property(property="transaction_date", type="string", format="date", example="2025-08-29"),
 *             @OA\Property(property="purpose", type="string", example="Follow-up check-up")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Transaction updated successfully."),
 *             @OA\Property(
 *                 property="transaction",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=12),
 *                 @OA\Property(property="transaction_type", type="string", example="Consultation"),
 *                 @OA\Property(property="transaction_mode", type="string", example="Walk-in"),
 *                 @OA\Property(property="transaction_date", type="string", format="date", example="2025-08-29"),
 *                 @OA\Property(property="purpose", type="string", example="Follow-up check-up"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-29T07:15:32Z"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-29T06:00:00Z")
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=404,
 *         description="Transaction not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Put(
 *     path="/api/transactions/vital/{id}",
 *     tags={"Transactions"},
 *     summary="Update an existing vital record",
 *     description="Update a patient's vital signs by ID",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID of the vital record",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="height", type="string", example="170cm"),
 *             @OA\Property(property="weight", type="string", example="65kg"),
 *             @OA\Property(property="bmi", type="string", example="22.5"),
 *             @OA\Property(property="temperature", type="string", example="36.8"),
 *             @OA\Property(property="waist", type="string", example="32in"),
 *             @OA\Property(property="pulse_rate", type="string", example="75"),
 *             @OA\Property(property="sp02", type="string", example="98%"),
 *             @OA\Property(property="heart_rate", type="string", example="72"),
 *             @OA\Property(property="blood_pressure", type="string", example="120/80"),
 *             @OA\Property(property="respiratory_rate", type="string", example="16"),
 *             @OA\Property(property="medicine", type="string", example="Paracetamol"),
 *             @OA\Property(property="LMP", type="string", example="2025-08-20")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Vital updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="vital updated successfully."),
 *             @OA\Property(
 *                 property="vital",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="height", type="string", example="170cm"),
 *                 @OA\Property(property="weight", type="string", example="65kg"),
 *                 @OA\Property(property="bmi", type="string", example="22.5"),
 *                 @OA\Property(property="temperature", type="string", example="36.8"),
 *                 @OA\Property(property="pulse_rate", type="string", example="75"),
 *                 @OA\Property(property="blood_pressure", type="string", example="120/80"),
 *                 @OA\Property(property="respiratory_rate", type="string", example="16")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Vital record not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 *
 *  @OA\Put(
 *     path="/api/transactions/{id}/update/status",
 *     tags={"Transactions"},
 *     summary="Update transaction status (Qualified or Unqualified)",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Transaction ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", example="Qualified")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction updated status successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Transaction updated status successfully."),
 *             @OA\Property(
 *                 property="transaction",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=15),
 *                 @OA\Property(property="status", type="string", example="Qualified"),
 *                 @OA\Property(property="transaction_type", type="string", example="Consultation"),
 *                 @OA\Property(property="transaction_date", type="string", example="2025-08-29"),
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction not found"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 *
 * * @OA\Put(
 *     path="/api/transactions/representative/{id}",
 *     tags={"Transactions"},
 *     summary="Update a representative record",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Representative ID"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="rep_name", type="string", example="Juan Dela Cruz"),
 *             @OA\Property(property="rep_relationship", type="string", example="Brother"),
 *             @OA\Property(property="rep_contact", type="string", example="09123456789"),
 *             @OA\Property(property="rep_barangay", type="string", example="San Isidro"),
 *             @OA\Property(property="rep_address", type="string", example="Purok 2, San Isidro"),
 *             @OA\Property(property="rep_purok", type="string", example="2"),
 *             @OA\Property(property="rep_street", type="string", example="Mabini St."),
 *             @OA\Property(property="rep_province", type="string", example="Davao del Sur"),
 *             @OA\Property(property="rep_city", type="string", example="Digos City")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Representative updated successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Representative not found"
 *     )
 * )
 */

class TransactionSwagger{}
