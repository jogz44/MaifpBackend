<?php

namespace App\Swagger;

/**
 * @OA\Get(
 *     path="/api/medications",
 *     tags={"Medications"},
 *     summary="Fetch patients qualified for Medication",
 *     description="This endpoint retrieves patients with transactions that are qualified for Medication today,
 *                  excluding those whose medication status is already 'Done'.
 *                  It groups the transactions by patient and attaches related consultation data.",
 *     @OA\Response(
 *         response=200,
 *         description="List of patients qualified for medication",
 *         @OA\JsonContent(type="array", @OA\Items(type="object"))
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch qualified transactions"
 *     )
 * )
 *
 *  @OA\Post(
 *     path="/api/medications/store",
 *     tags={"Medications"},
 *     summary="Create a new medication record",
 *     description="Stores a new medication record for a given transaction, including details such as
 *                  item description, quantity, unit, user, and amount.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"transaction_id","item_description","quantity","unit","amount"},
 *             @OA\Property(property="transaction_id", type="integer", example=101),
 *             @OA\Property(property="item_description", type="string", example="Paracetamol 500mg"),
 *             @OA\Property(property="quantity", type="integer", example=10),
 *             @OA\Property(property="unit", type="string", example="tablet"),
 *             @OA\Property(property="user_id", type="integer", example=5),
 *             @OA\Property(property="amount", type="number", format="float", example=250.50)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Medication created successfully",
 *         @OA\JsonContent(type="object")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/medications/status",
 *     tags={"Medications"},
 *     summary="Update or create a medication status",
 *     description="Updates or creates a medication status for the given transaction ID.
 *                  Status must be either 'Done' or 'Pending'.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"transaction_id","status"},
 *             @OA\Property(property="transaction_id", type="integer", example=101),
 *             @OA\Property(property="status", type="string", enum={"Done","Pending"}, example="Done")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Medication status updated successfully",
 *         @OA\JsonContent(type="object")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */

class  MedicationSwagger {}
