<?php

namespace App\Swagger;


/**
 *
 * @OA\Get(
 *     path="/api/laboratory",
 *     tags={"Laboratory"},
 *     summary="Fetch patients qualified for Laboratory",
 *     description="Retrieves patients who are qualified for laboratory services today,
 *                  excluding those who already have laboratory records marked as 'Done'.
 *                  Includes related patient, vitals, consultations, and laboratories.",
 *     @OA\Response(
 *         response=200,
 *         description="List of patients qualified for laboratory",
 *         @OA\JsonContent(type="array", @OA\Items(type="object"))
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Failed to fetch qualified laboratory transactions"
 *     )
 * )
 *
 * * @OA\Post(
 *     path="/api/laboratory/update/{id}",
 *     tags={"Laboratory"},
 *     summary="Update status of laboratories under a transaction",
 *     description="Updates the status (Pending, Done, Returned) of all laboratories associated with a given transaction.
 *                  If status is 'Returned', the linked consultation will also be updated to Returned.",
 *     @OA\Parameter(
 *         name="transactionId",
 *         in="path",
 *         required=true,
 *         description="ID of the transaction",
 *         @OA\Schema(type="integer", example=101)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status"},
 *             @OA\Property(property="status", type="string", enum={"Done","Returned","Pending"}, example="Returned")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Laboratory statuses updated successfully",
 *         @OA\JsonContent(type="object")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No laboratories found for this transaction"
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 *
 * * @OA\Post(
 *     path="/api/Laboratory/store",
 *     tags={"Laboratory"},
 *     summary="Store new laboratory records for a transaction",
 *     description="Stores multiple laboratory records for a patient’s transaction,
 *                  each with type, amount, and optional status.
 *                  Automatically links to a consultation if present.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"transaction_id","laboratories"},
 *             @OA\Property(property="transaction_id", type="integer", example=101),
 *             @OA\Property(
 *                 property="laboratories",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     required={"laboratory_type","amount"},
 *                     @OA\Property(property="laboratory_type", type="string", example="X-Ray"),
 *                     @OA\Property(property="amount", type="number", format="float", example=500.00),
 *                     @OA\Property(property="status", type="string", enum={"Pending","Returned","Done"}, example="Pending")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Laboratories stored successfully",
 *         @OA\JsonContent(type="object")
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 */


class   LaboratorySwagger {}
