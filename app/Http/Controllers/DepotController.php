<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/** 
 * @OA\Schema(
 *   schema = "DepotSchema",
 *   title = "Depot Model",
 *   description = "Depot Model",
 *   @OA\Property(
 *     property="id",
 *     type="uint64",
 *     example="1",
 *     description="depot primary key"
 *   ),
 *   @OA\Property(
 *     property="location",
 *     type="string",
 *     example="tunis, sfax route tanyour km6",
 *     description="depot location (country, city, address)"
 *   ),
 *   @OA\Property(
 *     property="size",
 *     type="string",
 *     example="120x40m²",
 *     description="depot size"
 *   ),
 *   @OA\Property(
 *     property="capacity",
 *     type="integer",
 *     example="500",
 *     description="depot capacity of storing"
 *   ),
 *   @OA\Property(
 *     property="type",
 *     type="string",
 *     example="food",
 *     description="depot products stored type"
 *   ),
 *   @OA\Property(
 *     property="isRented",
 *     type="boolean",
 *     example="1",
 *     description="depot rented verifier"
 *   ),
 *   @OA\Property(
 *     property="rent",
 *     type="float",
 *     example="1520.47",
 *     description="depot renting cost"
 *   )
 * )
 */


/**
 * @OA\Parameter(
 *   parameter="id",
 *   name="id",
 *   description="depot id",
 *   in="query",
 *   @OA\Schema(
 *     type="number"
 *   )
 * ),
 */


class DepotController extends Controller 
{
    /**
     * @OA\Get(
     *   tags={"Depots"},
     *   path="/depots/all",
     *   summary="Return the list of depots",
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/DepotSchema")
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not Found",
     *     @OA\JsonContent(ref="#/components/schemas/DepotSchema")
     *   )
     * )
     */
    public function index() : JsonResponse {
        // Get depots from database
        $depots = DB::select('select * from depots order by id desc');

        // Check if query returns data
        if($depots != null) 
            return response()->json(['data' => $depots],200);
        else 
            return response()->json("There is no depots for the moment",404);        
    }



    /**
     * @OA\Post(
     *   tags={"Depots"},
     *   path="/depots/create",
     *   summary="Depot Create",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       required={"xxx"},
     *       @OA\Property(
     *           property="location",
     *           type="string"
     *      ),
     *      @OA\Property(
     *          property="size",
     *          type="string"
     *      ),
     *      @OA\Property(
     *          property="capacity",
     *          type="integer"
     *      ),
    *       @OA\Property(
    *           property="type",
    *           type="string"
    *      ),
    *       @OA\Property(
    *           property="isRented",
    *           type="boolean"
    *       ),
    *       @OA\Property(
    *           property="rent",
    *           type="float"
    *       )
    *  )
    * ),
    *   @OA\Response(
    *     response=201,
    *     description="OK",
    *     @OA\JsonContent(ref="#/components/schemas/DepotSchema")
    *   ),
    *   @OA\Response(response=422, description="Unprocessable Entity")
    * )
    */
    public function store(Request $request) : JsonResponse {
        // Validate given data
        $validated = $this->ValidateRequest($request);

        // Check if data is valid
        if ($validated) {
            // Extract data from request 
            $req_data = $request->all();
            $data = $this->Extract($req_data);
            // Insert given data into database
            $insert = DB::insert('insert into depots (location,size,capacity,type,isRented,rent) values (?, ?,?,?,?,?)', $data);
            
            // Check if insert query done with success
            if ($insert) 
                return response()->json("inserted with success!",201);
            else 
                return response()->json("data has not inserted into database",500);
        }
        else 
            return response()->json("data input not validated",422);

    }



    /**
     * @OA\Get(
     *   tags={"Depots"},
     *   path="/depots/depot/{id}",
     *   summary="Depot Show",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/DepotSchema")
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not Found",
     *     @OA\JsonContent(ref="#/components/schemas/DepotSchema")
     *   ) 
     * )
     */
    public function show(int $id) : JsonResponse {
        // Get depot from database
        $depot = DB::select('select * from depots where id = ?', [$id]);

        // Check if query return data
        if($depot != null) 
            return response()->json(["data" => $depot],200);
        else 
            return response()->json("There is no depot with the given information!",404);
    }

    
    /**
     * @OA\Put(
     *   tags={"Depots"},
     *   path="/depots/update/{id}",
     *   summary="Depot update",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="location", type="string"),
     *       @OA\Property(property="size", type="string"),
     *       @OA\Property(property="capacity", type="integer"),
     *       @OA\Property(property="type", type="string"),
     *       @OA\Property(property="isRented", type="boolean"),
     *       @OA\Property(property="rent", type="integer")
     *     )
     *   ),
     *   @OA\Response(
     *     response=204,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/DepotSchema")
     *   ),
     *   @OA\Response(response=404, description="Not Found"),
     *   @OA\Response(response=422, description="Unprocessable Entity"),
     *   @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function update(Request $request, int $id) : JsonResponse {
        // Validate  given data 
        $validated = $this->ValidateRequest($request);
        // Check if given data is validated
        if($validated) {
            // Check if depot with given id exists
            $depot = DB::select('select * from depots where id = ?', [$id]);
            // Check if query do not return null 
                if($depot != null) {
                    // Extract data 
                    $req_data = $request->all();
                    $data = $this->ExtractWithId($req_data,$id);
                    // Update depot
                    $update = DB::update('update depots set location = ?, size = ?, capacity = ?, type = ?, isRented = ?, rent = ? where id = ?', $data);
                    // Check if update query done with success
                    if($update) 
                        return response()->json("data has been updated with success",204);
                    else 
                        return response()->json("sorry we can't update this depot for the moment!",500);
                } 
                else 
                    return response()->json("This depot not found in database",404);       
        }
    }


    /**
     * @OA\Delete(
     *   tags={"Depots"},
     *   path="/depots/delete/{id}",
     *   summary="Depot Delete",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\Response(response=204, description="OK"),
     *   @OA\Response(response=404, description="Not Found"),
     *   @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function delete(int $id) : JsonResponse {
        // Check if depot with given id exists
        $depot = DB::select('select * from depots where id = ?', [$id]);
        // Check if query do not return null
            if($depot != null) {
                // Delete depot
                $delete = DB::delete('delete from depots where id = ?', [$id]);
                // Check if delete done with success 
                if($delete) 
                    return response()->json("Depot has been deleted with success",204);
                else 
                    return response()->json("Sorry we can't delete your depot for the moment!",500);
            }
            else 
                return response()->json("This depot do not exist in the database",404);
    }


    /**
     * Validate a given request
     * @var boolean
     */
    private function ValidateRequest($request)  : bool {
        $validated = $this->validate($request, [
            'location' => 'required|string|min:2',
             'size' => 'required|string|min:3',
             'capacity' => 'required|integer|min:1',
             'type' => 'required|string|min:2',
             'isRented' => 'integer|min:1',
             
        ]); 

        if($validated) 
            return true;
        else 
            return false;
    }

    /** 
     * Extract data values from given request 
     * @var array 
     */
    private function Extract(array $data) : array {
        return $data  =  [
            $data["location"],
            $data["size"],
            $data["capacity"],
            $data["type"],
            $data["isRented"],
            $data["rent"],
        ];
    }


    /**
     * Extract data values from given request and append a given id 
     * @var array 
     */
    private function ExtractWithId(array $data, int $id) : array {
        return $data  =  [
            $data["location"],
            $data["size"],
            $data["capacity"],
            $data["type"],
            $data["isRented"],
            $data["rent"],
            $id,
        ];
    }
}



