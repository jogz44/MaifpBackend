<?php

namespace App\Swagger;

/**
 *
 * @OA\Get(
 *     path="/api/laboratory/exam/index",
 *     tags={"Laboratory Examination"},
 *     summary="Get all laboratory examinations",
 *     description="Fetches all available laboratory examinations from the library.",
 *     @OA\Response(
 *         response=200,
 *         description="List of laboratory examinations",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="item_id", type="string", example="LAB001"),
 *             @OA\Property(property="item_description", type="string", example="Complete Blood Count"),
 *             @OA\Property(property="service_fee", type="number", example=500.00),
 *             @OA\Property(property="amount", type="number", example=600.00),
 *             @OA\Property(property="created_at", type="string", example="2025-09-24T12:34:56Z"),
 *             @OA\Property(property="updated_at", type="string", example="2025-09-24T12:34:56Z")
 *         ))
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/laboratory/exam/store",
 *     tags={"Laboratory Examination"},
 *     summary="Create a new laboratory examination",
 *     description="Stores a new laboratory examination record in the library.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"item_id","item_description","service_fee","amount"},
 *             @OA\Property(property="item_id", type="string", example="LAB002"),
 *             @OA\Property(property="item_description", type="string", example="Urinalysis"),
 *             @OA\Property(property="service_fee", type="number", example=300.00),
 *             @OA\Property(property="amount", type="number", example=350.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Laboratory examination created successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=2),
 *             @OA\Property(property="item_id", type="string", example="LAB002"),
 *             @OA\Property(property="item_description", type="string", example="Urinalysis"),
 *             @OA\Property(property="service_fee", type="number", example=300.00),
 *             @OA\Property(property="amount", type="number", example=350.00),
 *             @OA\Property(property="created_at", type="string", example="2025-09-24T13:00:00Z"),
 *             @OA\Property(property="updated_at", type="string", example="2025-09-24T13:00:00Z")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/laboratory/exam/update/{lib_laboratory_examination_id}",
 *     tags={"Laboratory Examination"},
 *     summary="Update an existing laboratory examination",
 *     description="Updates a laboratory examination record by ID.",
 *     @OA\Parameter(
 *         name="lib_laboratory_examination_id",
 *         in="path",
 *         required=true,
 *         description="ID of the laboratory examination",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"item_id","item_description","service_fee","amount"},
 *             @OA\Property(property="item_id", type="string", example="LAB003"),
 *             @OA\Property(property="item_description", type="string", example="Fecalysis"),
 *             @OA\Property(property="service_fee", type="number", example=400.00),
 *             @OA\Property(property="amount", type="number", example=450.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Laboratory examination updated successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="item_id", type="string", example="LAB003"),
 *             @OA\Property(property="item_description", type="string", example="Fecalysis"),
 *             @OA\Property(property="service_fee", type="number", example=400.00),
 *             @OA\Property(property="amount", type="number", example=450.00),
 *             @OA\Property(property="updated_at", type="string", example="2025-09-24T14:10:00Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Laboratory examination not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/laboratory/exam/delete/{lib_laboratory_examination_id}",
 *     tags={"Laboratory Examination"},
 *     summary="Delete a laboratory examination",
 *     description="Deletes a laboratory examination record by ID.",
 *     @OA\Parameter(
 *         name="lib_laboratory_examination_id",
 *         in="path",
 *         required=true,
 *         description="ID of the laboratory examination",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Laboratory examination deleted successfully",
 *         @OA\JsonContent(
 *             type="object",
 *             @OA\Property(property="message", type="string", example="Deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Laboratory examination not found"
 *     )
 * )
 */
class LaboratoryExaminationSwagger {}
