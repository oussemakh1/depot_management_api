<?php 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/** 
 * @OA\Schema(
 *   schema = "InvoiceSchema",
 *   title = "Invoice Model",
 *   description = "Invoice Model",
 *   @OA\Property(
 *     property="product_id",
 *     type="integer",
 *     example="1",
 *     description="product id"
 *   ),
 *   @OA\Property(
 *     property="provider_id",
 *     type="integer",
 *     example="1",
 *     description="provider id"
 *   ),
 *   @OA\Property(
 *     property="status",
 *     type="string",
 *     example="pending",
 *     description="invoice status"
 *   ),
 *   @OA\Property(
 *     property="quantity",
 *     type="integer",
 *     example="10",
 *     description="product quantity"
 *   ),
 *   @OA\Property(
 *     property="recivedat",
 *     type="date",
 *     example="2021/08/11 11:045:20",
 *     description="product delivery date"
 *   ),
 *   @OA\Property(
 *     property="price",
 *     type="decimal",
 *     example="455.2",
 *     description="product price"
 *   ),
 *   @OA\Property(
 *     property="discount",
 *     type="decimal",
 *     example="1500",
 *     description="product discount"
 *   ),
 *   @OA\Property(
 *     property="total",
 *     type="decimal",
 *     example="1500.57",
 *     description="invoice total"
 *   )
 * )
 */


class InvoiceController extends Controller 
{
    /**
     * @OA\Get(
     *   tags={"Invoices"},
     *   path="/invoices/all",
     *   summary="Return list of invoices",
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(
     *         property="data",
     *         type="array",
     *         @OA\Items(ref="#/components/schemas/InvoiceSchema")
     *       ),
     *     )
     *   )
     * )
     */
    public function index() : JsonResponse {
        // Get invoices from database
        $invoices = DB::select('select * from invoices order by id desc');

         // Check if query return data
        if($invoices != null) 
            return response()->json(["data" => $invoices],200);
        else 
            return response()->json("There is no invoices in the database",400);

    }

    /**
     * @OA\Post(
     *   tags={"Invoices"},
     *   path="/invoices/create",
     *   summary="Invoice store",
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="product_id", type="integer"),
     *       @OA\Property(property="provider_id", type="integer"),
     *       @OA\Property(property="status", type="string"),
     *       @OA\Property(property="quantity", type="integer"),
     *       @OA\Property(property="recivedat", type="date"),
     *       @OA\Property(property="price", type="decimal"),
     *       @OA\Property(property="discount", type="decimal"),
     *       @OA\Property(property="total", type="decimal")
     *     )
     *   ),
     *   @OA\Response(
     *     response=201,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/InvoiceSchema")
     *   ),
     *   @OA\Response(response=500, description="Internal server error"),
     *   @OA\Response(response=400, description="Bad Request")
     * )
     */
    public function store(Request $request) : JsonResponse {
        // Validate request
        $validated = $this->ValidateRequest($request);
        if($validated) {
            // Extract data 
            $req_data = $request->all();
            $data  = $this->ExtractData($req_data);
            // Insert invoice in the database
            $insert = DB::insert('insert into invoices (product_id, provider_id, status, quantity, recivedat, price, discount, total) values (?, ?, ?, ?, ?, ?, ?, ?)', $data);
            // Check if insert query done with success 
            if($insert) return response()->json("Invoice has been inserted",201);
                else return  response()->json("Invoice has not been inserted",500);
        }
        else return response()->json("Given data has not been validated!",400);
    }



    /**
     * @OA\Get(
     *   tags={"Invoices"},
     *   path="/invoices/invoice/{id}",
     *   summary="Invoice show",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/InvoiceSchema")
     *   ),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function show(int $id) : JsonResponse {
        // Get invoice from database
        $invoice = DB::select('select * from invoices where id = ?', [$id]);

        // Check if query return data
        if($invoice != null) return response()->json(["data" => $invoice],200);
            else return response()->json("This invoice do not exist in the database",404);

    }




    /**
     * @OA\Put(
     *   tags={"Invoices"},
     *   path="/invoices/update/{id}",
     *   summary="Invoice update",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="product_id", type="integer"),
     *       @OA\Property(property="provider_id", type="integer"),
     *       @OA\Property(property="status", type="string"),
     *       @OA\Property(property="quantity", type="integer"),
     *       @OA\Property(property="recivedat", type="date"),
     *       @OA\Property(property="price", type="decimal"),
     *       @OA\Property(property="discount", type="decimal"),
     *       @OA\Property(property="total", type="decimal")
     *      )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="OK",
     *     @OA\JsonContent(ref="#/components/schemas/InvoiceSchema")
     *   ),
     *   @OA\Response(response=404, description="Not Found"),
     *   @OA\Response(response=422, description="Unprocessable Entity"),
     *   @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function update(Request $request, int $id) : JsonResponse {
        // Check if invoice exists in the database
        $invoice = DB::select('select id from invoices where id = ?', [$id]);

        // Check if query return data
        if($invoice != null) {
            // Validate request
            $validated = $this->ValidateRequest($request);
            // Check if request validated
            if($validated) {
                // Extract data
                $req_data = $request->all();
                $data = $this->ExtractDataWithId($req_data, $id);
                // Update invoice
                $update = DB::update('update invoices set product_id = ?, provider_id = ?, status = ?, quantity = ?, recivedat = ?, price = ?, discount = ?, total = ? where id = ?', $data);
                // Check if update query done with success
                if($update)  return response()->json("Invoice has been updated",200);
                    else return response()->json("Invoice has not been updated", 500);
                }  
                else return response()->json("Given data has not been validated",422);
            }
            else return response()->json("This invoice does not exist in the database",404);
    }


    /**
     * @OA\Delete(
     *   tags={"Invoices"},
     *   path="/invoices/delete/{id}",
     *   summary="Invoice delete",
     *   @OA\Parameter(ref="#/components/parameters/id"),
     *   @OA\Response(response=200, description="OK"),
     *   @OA\Response(response=500, description="Internal server error"),
     *   @OA\Response(response=404, description="Not Found")
     * )
     */
    public function delete(int $id) : JsonResponse {
        // Check if invoice exist in the database
        $invoice = DB::select('select id from invoices where id = ?', [$id]);
    
        // Check if query return data
        if($invoice != null) {
            // Delete invoice 
            $delete = DB::delete('delete from invoices where id = ?', [$id]);
            // Check if delete query has been done with success
            if($delete) return response()->json("Invoice has been deleted",200);
                else return response()->json("Invoice has not been deleted",500);
            }
            else return response()->json("Invoice does not exist in the database",440);
    }


    /** 
     * Validate data from given request
     * @var boolean
     */
    private function ValidateRequest($request) {
        $validated = $this->validate($request, [
            "product_id" => 'required|integer',
            "provider_id" => 'required|integer',
            "status" => 'required|string',
            "quantity" => 'required',
            "recivedat" => 'required|date',
            "price" => 'required|integer',
            "discount" => 'required|integer',
            "total" => 'required|integer',

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
            $data["product_id"],
            $data["provider_id"],
            $data["status"],
            $data["quantity"],
            $data["recivedat"],
            $data["price"],
            $data["discount"],
            $data["total"],
        ];
    }


        /**
     * Extract data from given request (update)
     * @var array
     */
    private function ExtractDataWithId(array $data, int $id) {
        return $data = [
            $data["product_id"],
            $data["provider_id"],
            $data["status"],
            $data["quantity"],
            $data["recivedat"],
            $data["price"],
            $data["discount"],
            $data["total"],
            $id,
        ];
    }
}