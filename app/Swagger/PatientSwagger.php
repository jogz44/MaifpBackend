<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/patients",
 *     tags={"Patients"},
 *     summary="Get all patients",
 *     @OA\Response(
 *         response=200,
 *         description="List of patients"
 *     )
 * )
 *
 *  * @OA\Get(
 *     path="/api/patients/master_list",
 *     tags={"Patients"},
 *     summary="Get all patients  lastest transaction fetch on the master list module",
 *     @OA\Response(
 *         response=200,
 *         description="Master list "
 *     )
 * )
 * * @OA\Get(
 *     path="/api/patients/count/badge",
 *     tags={"Patients"},
 *     summary="Count patients",
 *     @OA\Response(
 *         response=200,
 *         description="Count the patients every module"
 *     )
 * )
 *  * @OA\Get(
 *     path="/api/patients/assessment",
 *     tags={"Patients"},
 *     summary="fetch all patient need to assess",
 *     @OA\Response(
 *         response=200,
 *         description="list of patient for assessment"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/patients/{id}",
 *     tags={"Patients"},
 *     summary="Get a patient with transactions",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Patient ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Patient details with transactions"
 *     )
 * )
 *

 *
 *
 *
 * @OA\Put(
 *     path="/api/patients/update/{id}",
 *     tags={"Patients"},
 *     summary="Update an existing patient",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="Patient ID",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="firstname", type="string", example="Juan"),
 *             @OA\Property(property="lastname", type="string", example="Dela Cruz"),
 *             @OA\Property(property="middlename", type="string", example="Santos"),
 *             @OA\Property(property="ext", type="string", example="Jr."),
 *             @OA\Property(property="birthdate", type="string", format="date", example="1990-01-01"),
 *             @OA\Property(property="contact_number", type="string", example="09123456789"),
 *             @OA\Property(property="age", type="integer", example=34),
 *             @OA\Property(property="gender", type="string", example="Male"),
 *             @OA\Property(property="is_not_tagum", type="boolean", example=false),
 *             @OA\Property(property="street", type="string", example="Purok 1"),
 *             @OA\Property(property="purok", type="string", example="2A"),
 *             @OA\Property(property="barangay", type="string", example="San Roque"),
 *             @OA\Property(property="city", type="string", example="Tagum"),
 *             @OA\Property(property="province", type="string", example="Davao del Norte"),
 *             @OA\Property(property="category", type="string", enum={"Child","Adult","Senior"}, example="Adult"),
 *             @OA\Property(property="is_pwd", type="boolean", example=false),
 *             @OA\Property(property="is_solo", type="boolean", example=false),
 *             @OA\Property(property="user_id", type="integer", example=5)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Patient updated successfully"
 *     )
 * )
 *
 * /**
 * @OA\Post(
 *     path="/api/patients/store",
 *     tags={"Patients"},
 *     summary="Create a patient with representative, transaction, and vitals",
 *     description="This endpoint creates a new patient record, a representative, a transaction, and the patient's vital signs in one request.",
 *
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"firstname","lastname","birthdate","gender","barangay","category","rep_name","transaction_type","transaction_mode","transaction_date","height","weight"},
 *             @OA\Property(property="firstname", type="string", example="Juan"),
 *             @OA\Property(property="lastname", type="string", example="Dela Cruz"),
 *             @OA\Property(property="middlename", type="string", example="Santos"),
 *             @OA\Property(property="ext", type="string", example="Jr."),
 *             @OA\Property(property="birthdate", type="string", format="date", example="1990-01-15"),
 *             @OA\Property(property="contact_number", type="string", example="09123456789"),
 *             @OA\Property(property="age", type="integer", example=35),
 *             @OA\Property(property="gender", type="string", example="Male"),
 *             @OA\Property(property="is_not_tagum", type="boolean", example=false),
 *             @OA\Property(property="street", type="string", example="San Pedro"),
 *             @OA\Property(property="purok", type="string", example="5"),
 *             @OA\Property(property="barangay", type="string", example="Poblacion"),
 *             @OA\Property(property="city", type="string", example="Tagum City"),
 *             @OA\Property(property="province", type="string", example="Davao del Norte"),
 *             @OA\Property(property="category", type="string", enum={"Child","Adult","Senior"}, example="Adult"),
 *             @OA\Property(property="is_pwd", type="boolean", example=false),
 *             @OA\Property(property="is_solo", type="boolean", example=false),
 *
 *             @OA\Property(property="rep_name", type="string", example="Maria Dela Cruz"),
 *             @OA\Property(property="rep_relationship", type="string", example="Mother"),
 *             @OA\Property(property="rep_contact", type="string", example="09987654321"),
 *             @OA\Property(property="rep_barangay", type="string", example="Magugpo"),
 *             @OA\Property(property="rep_address", type="string", example="Blk 5 Lot 10"),
 *             @OA\Property(property="rep_purok", type="string", example="2"),
 *             @OA\Property(property="rep_street", type="string", example="Quezon St."),
 *             @OA\Property(property="rep_city", type="string", example="Tagum City"),
 *             @OA\Property(property="rep_province", type="string", example="Davao del Norte"),
 *
 *             @OA\Property(property="transaction_type", type="string", example="Consultation"),
 *             @OA\Property(property="transaction_mode", type="string", example="Walk-in"),
 *             @OA\Property(property="transaction_date", type="string", format="date", example="2025-08-29"),
 *             @OA\Property(property="purpose", type="string", example="Routine checkup"),
 *
 *             @OA\Property(property="height", type="string", example="170 cm"),
 *             @OA\Property(property="weight", type="string", example="65 kg"),
 *             @OA\Property(property="bmi", type="string", example="22.5"),
 *             @OA\Property(property="temperature", type="string", example="36.8"),
 *             @OA\Property(property="pulse_rate", type="string", example="72"),
 *             @OA\Property(property="sp02", type="string", example="98"),
 *             @OA\Property(property="heart_rate", type="string", example="75"),
 *             @OA\Property(property="blood_pressure", type="string", example="120/80"),
 *             @OA\Property(property="respiratory_rate", type="string", example="18"),
 *             @OA\Property(property="medicine", type="string", example="N/A"),
 *             @OA\Property(property="LMP", type="string", example="2025-08-01")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Patient, representative, transaction, and vitals created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Patient, transaction, and vitals created successfully."),
 *             @OA\Property(property="patient", type="object"),
 *             @OA\Property(property="transaction", type="object"),
 *             @OA\Property(property="vital", type="object"),
 *             @OA\Property(property="representative", type="object"),
 *             @OA\Property(property="transaction_number", type="string", example="2025-08-29-00108")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=409,
 *         description="Duplicate patient record found",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Patient already has a record. Please add a new transaction instead."),
 *             @OA\Property(property="patient", type="object")
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=500,
 *         description="Unexpected error",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="An unexpected error occurred"),
 *             @OA\Property(property="errors", type="string", example="SQLSTATE[23000]...")
 *         )
 *     )
 * )
 *
 *  * @OA\Post(
 *     path="/api/patients/add-transaction",
 *     summary="Add a new transaction and vitals for an existing patient",
 *     tags={"Patients"},
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

 */

class PatientSwagger {}
