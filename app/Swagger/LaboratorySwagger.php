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
 ** @OA\Post(
 *     path="/status",
 *     summary="Update Laboratory Status",
 *     description="This endpoint updates the status of a laboratory record linked to a consultation based on the given transaction ID." status will be accpect only Returned and Done,
 *     operationId="updateLaboratoryStatus",
 *     tags={"Laboratory"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"status","transaction_id"},
 *             @OA\Property(property="status", type="string", enum={"Done","Returned"}, example="Returned", description="Status of the laboratory"),
 *             @OA\Property(property="transaction_id", type="integer", example=1, description="Transaction ID associated with the laboratory")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Laboratory status updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="success", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Laboratory status under this transaction updated successfully."),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="transaction_id", type="integer", example=1),
 *                 @OA\Property(property="status", type="string", example="Returned"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-09-05T10:20:30.000000Z"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-05T10:10:00.000000Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation Error",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="The given data was invalid."),
 *             @OA\Property(property="errors", type="object",
 *                 @OA\Property(property="status", type="array", @OA\Items(type="string", example="The status field is required.")),
 *                 @OA\Property(property="transaction_id", type="array", @OA\Items(type="string", example="The transaction id field is required."))
 *             )
 *         )
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
 *
 *
 *
 *
 *
 *
 * @OA\Get(
 *     path="/api/laboratory/index/lab_services",
 *     tags={"Library Laboratory"},
 *     summary="Fetch all laboratory services",
 *     description="Retrieves the list of all available laboratory services stored in the library.",
 *     @OA\Response(
 *         response=200,
 *         description="List of laboratory services",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="laboratory_name", type="string", example="X-Ray"),
 *             @OA\Property(property="fee", type="number", format="float", example=500.00)
 *         ))
 *     )
 * )
 *
 *
 * @OA\Post(
 *     path="/api/laboratory/store/lab_services",
 *     tags={"Library Laboratory"},
 *     summary="Store new laboratory service",
 *     description="Stores a new laboratory service in the library, including name and amount.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"lab_name","lab_amount"},
 *             @OA\Property(property="lab_name", type="string", example="MRI Scan"),
 *             @OA\Property(property="lab_amount", type="number", format="float", example=2500.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Laboratory service stored successfully",
 *         @OA\JsonContent(type="object",
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(property="laboratory", type="object",
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="lab_name", type="string", example="MRI Scan"),
 *                 @OA\Property(property="lab_amount", type="number", format="float", example=2500.00)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 ** @OA\Post(
 *     path="/api/laboratory/update/lab_services/{lib_laboratory}",
 *     tags={"Library Laboratory"},
 *     summary="Update a laboratory service",
 *     description="Updates an existing laboratory service in the library.",
 *     @OA\Parameter(
 *         name="lib_laboratory",
 *         in="path",
 *         required=true,
 *         description="ID of the laboratory service",
 *         @OA\Schema(type="integer", example=5)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="lab_name", type="string", example="Updated X-Ray"),
 *             @OA\Property(property="lab_amount", type="number", format="float", example=600.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Laboratory service updated successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Laboratory service not found"
 *     )
 * )
 *
 * @OA\Delete(
 *   path="/api/laboratory/delete/lab_services/{lib_laboratory}",
 *     tags={"Library Laboratory"},
 *     summary="Delete a laboratory service",
 *     description="Deletes an existing laboratory service from the library.",
 *     @OA\Parameter(
 *         name="lib_laboratory",
 *         in="path",
 *         required=true,
 *         description="ID of the laboratory service",
 *         @OA\Schema(type="integer", example=7)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Laboratory service deleted successfully",
 *         @OA\JsonContent(type="object",
 *             @OA\Property(property="message", type="string", example="successfully delete"),
 *             @OA\Property(property="laboratory", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Laboratory service not found"
 *     )
 * )
 */


class   LaboratorySwagger {}
