<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\Interfaces\CustomerRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormInterface;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\DBAL\Exception;

class CustomerController extends AbstractController {

    private CustomerRepositoryInterface $repository;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    public function __construct(
        CustomerRepositoryInterface $repository,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    )  {
        $this->repository = $repository;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * 
     * Get all customers
     * 
     * @OA\Post(
     *     path="/read",
     *     summary="Muestra todos los clientes",
     *     description="Muestra todos los clientes",
     *     operationId="read",
     *     @OA\Response(
     *         response=200,
     *         description="Clientes obtenidos exitosamente"
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="No es posible conectar con la base de datos"
     *     )
     * )
     */
    public function read(): Response
    {
        try {
            $customers = $this->repository->getAllCustomers();
        }
        catch(Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        }
        
        $jsonData = $this->serializer->serialize($customers, 'json');

        return new Response($jsonData, Response::HTTP_OK, ['Content-Type', 'application/json']);
    }

    /**
     * 
     * Create a new customer
     * 
     * @OA\Post(
     *     path="/create",
     *     summary="Crear un nuevo cliente",
     *     description="Crea un nuevo cliente",
     *     operationId="create",
     *     @OA\Response(
     *         response=200,
     *         description="Cliente creado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos de la petición incorrectos"
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="No es posible conectar con la base de datos"
     *     ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="form[name]",
     *                     description="Customer name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="form[address]",
     *                     description="Customer address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="form[province]",
     *                     description="Customer province",
     *                     type="string",
     *                 ), 
     *                 @OA\Property(
     *                     property="form[cif]",
     *                     description="Customer CIF",
     *                     type="string",
     *                 )
     *             )
     *         )
     *     ) 
     * )
     */
    public function create(Request $request): Response
    {
        $requestData = json_decode($request->getContent(), true);

        $newCustomer = new Customer();
        $newCustomer->setName($requestData['name']);
        $newCustomer->setAddress($requestData['address']);
        $newCustomer->setProvince($requestData['province']);
        $newCustomer->setCif($requestData['cif']);

        $errors = $this->validator->validate($newCustomer);

        if(count($errors) > 0) {
            return new Response("Hay campos en blanco", Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->repository->save($newCustomer);
        }
        catch(Exception $e) {
            return new Response($e->getMessage(), Response::HTTP_SERVICE_UNAVAILABLE);
        }
        
        return new Response("Cliente creado correctamente", Response::HTTP_OK);
    }

        /**
     * 
     * Update Customer information
     * 
     * @OA\Post(
     *     path="/update/{id}",
     *     summary="Actualizar la informacion de un cliente",
     *     description="Actualizar la informacion de un cliente",
     *     operationId="update",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del cliente",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ), 
     *     @OA\Response(
     *         response=200,
     *         description="Informacion del cliente actualizada exitosamente"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Datos de la petición incorrectos"
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="No es posible conectar con la base de datos"
     *     ),
     *     @OA\RequestBody(
     *         description="Input data format",
     *         @OA\MediaType(
     *             mediaType="application/x-www-form-urlencoded",
     *             @OA\Schema(
     *                 type="object",
     *                 @OA\Property(
     *                     property="form[name]",
     *                     description="Customer name",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="form[address]",
     *                     description="Customer address",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="form[province]",
     *                     description="Customer province",
     *                     type="string",
     *                 ), 
     *                 @OA\Property(
     *                     property="form[cif]",
     *                     description="Customer CIF",
     *                     type="string",
     *                 )
     *             )
     *         )
     *     ) 
     * )
     */
    public function update(Request $request, int $id): Response
    {
        try {
            $requestData = json_decode($request->getContent(), true);

            $customer = $this->repository->getCustomer($id);
            $customer->setName($requestData['name']);
            $customer->setAddress($requestData['address']);
            $customer->setProvince($requestData['province']);
            $customer->setCif($requestData['cif']);

            $errors = $this->validator->validate($customer);

            if(count($errors) > 0) {
                return new Response(json_encode(['message' => 'Hay campos en blanco']), Response::HTTP_BAD_REQUEST);
            }
    
            $this->repository->save($customer);
            
            return new Response(json_encode(['message' => 'Cliente actualizado correctamente']), Response::HTTP_OK);
        }
        catch(Exception $e) {
            return new Response(json_encode(['message' => $e->getMessage()]), Response::HTTP_SERVICE_UNAVAILABLE);
        }
    }

    /**
     * 
     * Delete an existing customer
     * 
     * @OA\Delete(
     *     path="/delete/{id}",
     *     summary="Eliminar un elemento por ID",
     *     description="Elimina un elemento en base a su ID.",
     *     operationId="delete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del elemento a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Elemento eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="No es posible conectar con la base de datos"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="El cliente no existe"
     *     )
     * )
     */
    public function delete(int $id): Response
    {
        try {
            $customer = $this->repository->getCustomer($id);
        }
        catch(Exception $e) {
            return new Response(json_encode(['message' => $e->getMessage()]), Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if(is_null($customer)) {
            return new Response(json_encode(['message' => 'El cliente con id '.$id.' no existe']), Response::HTTP_NOT_FOUND);
        }
       
        $this->repository->delete($id);
        
        return new Response(json_encode(['message' => 'Cliente borrado correctamente']), Response::HTTP_OK);
    }

    /**
     * 
     * Get a single customer
     * 
     * @OA\Post(
     *     path="/delete/{id}",
     *     summary="Eliminar un elemento por ID",
     *     description="Elimina un elemento en base a su ID.",
     *     operationId="delete",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID del elemento a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Elemento eliminado exitosamente"
     *     ),
     *     @OA\Response(
     *         response=503,
     *         description="No es posible conectar con la base de datos"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="El cliente no existe"
     *     )
     * )
     */
    public function getCustomer(int $id): Response
    {
        try {
            $customer = $this->repository->getCustomer($id);
        }
        catch(Exception $e) {
            return new Response(json_encode(['message' => $e->getMessage()]), Response::HTTP_SERVICE_UNAVAILABLE);
        }

        if(is_null($customer)) {
            return new Response(json_encode(['message' => 'El cliente con id '.$id.' no existe']), Response::HTTP_NOT_FOUND);
        }

        return new Response(json_encode($customer->toArray()), Response::HTTP_OK, ['Content-Type', 'application/json']);
    }
}
