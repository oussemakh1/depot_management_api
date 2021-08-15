<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Schema(
 *   schema = "ProviderSchema",
 *   title = "Provider Model",
 *   description = "Provider Model",
 *   @OA\Property(
 *     property="name",
 *     type="string",
 *     example="samsung corp",
 *     description="provider name"
 *   ),
 *   @OA\Property(
 *     property="email",
 *     type="string",
 *     example="contact@samsung.com",
 *     description="provider email"
 *   ),
 *   @OA\Property(
 *     property="fax",
 *     type="string",
 *     example="+216 74 523 658",
 *     description="provider fax"
 *   ),
 *   @OA\Property(
 *     property="phone",
 *     type="string",
 *     example="+216 55 574 426",
 *     description="provider phone"
 *   ),
 *   @OA\Property(
 *     property="mat",
 *     type="string",
 *     example="Az258xfvbz",
 *     description="provider registration number"
 *   ),
 *   @OA\Property(
 *     property="address",
 *     type="string",
 *     example="Sfax, route gremda km2",
 *     description="provider location"
 *   ),
 *   @OA\Property(
 *     property="country",
 *     type="string",
 *     example="tunisia",
 *     description="provider country"
 *   )
 * )
 * )
 */




class ProviderController extends Controller 
{

    /**
     * @OA\Get(
     *   tags={"Providers"},
     *   path="/providers/all",
     *   summary="Return list of providers",
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/ProviderSchema")
     *       ),
     *     )
     *   ),
     *   @OA\Response(
     *     response=404,
     *     description="Not Found",
     *   )
     * )
     */
    public function index() : JsonResponse {
        // Get providers from database
        $providers = DB::select('select * from providers order by id desc');
     
        // Check if query return data
        if($providers != null)
            return response()->json(["data" => $providers],200);
        else 
            return response()->json("There is no providers in the database", 404);
        
    }




    /**
     * @OA\Post(
     *   tags={"Providers"},
     *   path="/providers/create",
     *   summary="Provider store",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="email", type="string"),
     *       @OA\Property(property="fax", type="string"),
     *       @OA\Property(property="phone", type="string"),
     *       @OA\Property(property="mat", type="string"),
     *       @OA\Property(property="address", type="string"),
     *       @OA\Property(property="country", type="string")
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/ProviderSchema")
     *   ),
     *   @OA\Response(response=422, description="Unprocessable Entity"),
     *   @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function store(Request $request) : JsonResponse {
        // Validate given requst
        $validated = $this->ValidateStoreRequest($request);
        // Check if request is validate
        if($validated) {
            // Extract given data
            $req_data = $request->all();
            $data = $this->ExtractData($req_data);
            // Insert provider in the database
            $insert = DB::insert('insert into providers (name,email,fax,phone,mat,address,country) values (?, ?, ?, ?, ?, ?, ?)', $data);
            // Check if insert query done with success
            if($insert) 
                return response()->json("Provider has been inserted",201);
            else 
                return response()->json("Sorry provider has not been insrted",500);
        }
        else 
            return response()->json("Given data not validated",422);
    }




    /**
     * @OA\Get(
     *   tags={"Providers"},
     *   path="/providers/provider/{id}",
     *   summary="Provider show",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/ProviderSchema")
     *   ),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(int $id) : JsonResponse {
        // Get provider form database
        $provider = DB::select('select * from providers where id = ?', [$id]);
       
        // Check if query return data
        if($provider != null) 
            return response()->json(["data" => $provider],200);
        else 
            return response()->json("This provider do not exist in the database",404);
    }



    /**
     * @OA\Put(
     *   tags={"Providers"},
     *   path="/providers/update/{id}",
     *   summary="Provider update",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="email", type="string"),
     *       @OA\Property(property="fax", type="string"),
     *       @OA\Property(property="phone", type="string"),
     *       @OA\Property(property="mat", type="string"),
     *       @OA\Property(property="address", type="string"),
     *       @OA\Property(property="country", type="string")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/ProviderSchema")
     *   ),
     *   @OA\Response(response=400, description="Bad Request"),
     *   @OA\Response(response=404, description="Not Found"),
     *   @OA\Response(response=422, description="Unprocessable Entity"),
     *   @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function update(Request $request, int $id) : JsonResponse {
        // Checki if provider exists in the database
        $provider = DB::select('select * from providers where id = ?', [$id]);
    
        // Check if query return data
        if($provider != null) {
            // Validate Request 
            $validated = $this->ValidateUpdateRequest($request, $id);
            // Check if request validated
            if($validated) {
                // Extract data 
                $req_data = $request->all();
                $data = $this->ExtractDataWithId($req_data,$id);
                // Update provider
                $update = DB::update('update providers set name = ?, email = ?, fax = ?, phone = ?, mat = ?, address = ?, country = ? where id = ?', $data);
                // Check if update done with success
                if($update)
                        return response()->json("Provider has been updated",200);
                    else 
                        return response()->json("Sorry we could'nt update provider",500); 
            }
            else 
                return response()->json("Given data has not been validated!",422);
        }
        else 
            return response()->json("This provider does not exist in the database", 404);
    }



    /**
     * @OA\Delete(
     *   tags={"Providers"},
     *   path="/providers/delete/{id}",
     *   summary="Provider delete",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=500, description="Internal server error"),
     *   @OA\Response(response=404, description="Not Found"),
     * )
     */
    public function delete(int $id) : JsonResponse  {
        // Check if provider exist in the database
        $provider = DB::select('select * from providers where id = ?', [$id]);
       
        // Check if query return data
        if($provider != null) {
            // Delete provider 
            $delete = DB::delete('delete from providers where id = ?', [$id]);
            // Check if delete done with success
            if($delete) 
                return response()->json("Provider has been delete with success",200);
            else 
                return response()->json("Provider has not been deleted",500);
        }
        else return response()->json("Provider does not exist in the database",404);
    }




    /**
     * Validate given request data (store)
     * @var boolean
     */
    private function ValidateStoreRequest($request) {
        $validated = $this->validate($request, [
            "name" => 'required|string|unique:providers',
            "email" => 'required|email|unique:providers',
            "fax" => 'string|min:6|unique:providers',
            "phone" => 'string|min:6|unique:providers',
            "mat" => 'string|unique:providers',
            "address" => 'required|string',
            "country" => 'required|string',
        ]);

        if($validated) 
            return true;
        else 
            return false;
    }


      /**
     * Validate given request data (update)
     * @var boolean
     */
    private function ValidateUpdateRequest($request, $id) {
        $validated = $this->validate($request, [
            "name" => 'required|string|unique:providers,name,'.$id,
            "email" => 'required|email|unique:providers,email,'.$id,
            "fax" => 'string|min:6|unique:providers,fax,'.$id,
            "phone" => 'string|min:6|unique:providers,phone,'.$id,
            "mat" => 'string|unique:providers,mat,'.$id,
            "address" => 'required|string',
            "country" => 'required|string',
        ]);

        if($validated) 
            return true;
        else 
            return false;
    }

    
    /**
     * Extract data from given request (store)
     * @var array 
     */
    private function ExtractData(array $data) {
        return $data = [
            $data["name"],
            $data["email"],
            $data["fax"],
            $data["phone"],
            $data["mat"],
            $data["address"],
            $data["country"],
        ];
    }



    /**
     * Extract data from given request (update)
     * @var array 
     */
    private function ExtractDataWithId(array $data, int $id) {
        return $data = [
            $data["name"],
            $data["email"],
            $data["fax"],
            $data["phone"],
            $data["mat"],
            $data["address"],
            $data["country"],
            $id,
        ];
    }

}