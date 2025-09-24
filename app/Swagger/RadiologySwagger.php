<?php

namespace App\Swagger;

/**
 *
 * @OA\Tag(
 *     name="Radiology",
 *     description="API Endpoints for Radiology Library"
 * )
 *
 * @OA\Get(
 *     path="/api/laboratory/radiology/index",
 *     tags={"Radiology"},
 *     summary="Fetch all radiology services",
 *     description="Retrieves all radiology services stored in the library.",
 *     @OA\Response(
 *         response=200,
 *         description="List of radiology services",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             type="object",
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="item_description", type="string", example="Chest X-Ray"),
 *             @OA\Property(property="service_fee", type="number", format="float", example=550.00),
 *             @OA\Property(property="amount", type="number", format="float", example=550.00)
 *         ))
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/laboratory/radiology/store",
 *     tags={"Radiology"},
 *     summary="Store new radiology service",
 *     description="Stores a new radiology service in the library.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"item_description","service_fee","amount"},
 *             @OA\Property(property="item_description", type="string", example="Chest X-Ray"),
 *             @OA\Property(property="service_fee", type="number", format="float", example=550.00),
 *             @OA\Property(property="amount", type="number", format="float", example=550.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Radiology service stored successfully",
 *         @OA\JsonContent(type="object",
 *             @OA\Property(property="message", type="string", example="success"),
 *             @OA\Property(property="radiology", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="item_description", type="string", example="Chest X-Ray"),
 *                 @OA\Property(property="service_fee", type="number", format="float", example=550.00),
 *                 @OA\Property(property="amount", type="number", format="float", example=550.00)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/laboratory/radiology/update/{lib_rad_id}",
 *     tags={"Radiology"},
 *     summary="Update a radiology service",
 *     description="Updates an existing radiology service in the library.",
 *     @OA\Parameter(
 *         name="lib_rad_id",
 *         in="path",
 *         required=true,
 *         description="ID of the radiology service",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="item_description", type="string", example="Updated Chest X-Ray"),
 *             @OA\Property(property="service_fee", type="number", format="float", example=600.00),
 *             @OA\Property(property="amount", type="number", format="float", example=600.00)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Radiology service updated successfully"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Radiology service not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/laboratory/radiology/delete/{lib_rad_id}",
 *     tags={"Radiology"},
 *     summary="Delete a radiology service",
 *     description="Deletes an existing radiology service from the library.",
 *     @OA\Parameter(
 *         name="lib_rad_id",
 *         in="path",
 *         required=true,
 *         description="ID of the radiology service",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Radiology service deleted successfully",
 *         @OA\JsonContent(type="object",
 *             @OA\Property(property="message", type="string", example="successfully deleted"),
 *             @OA\Property(property="radiology", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Radiology service not found"
 *     )
 * )
 */

class RadiologySwagger {}
