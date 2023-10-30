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

class CustomerController extends AbstractController {

    private CustomerRepositoryInterface $repository;

    public function __construct(
        CustomerRepositoryInterface $repository
    )  {
        $this->repository = $repository;
    }

    /**
     * 
     * Muestra todos los clientes
     * 
     * @OA\Post(
     *     path="/index",
     *     summary="Muestra todos los clientes",
     *     description="Muestra todos los clientes",
     *     operationId="index",
     *     @OA\Response(
     *         response=200,
     *         description="Elemento eliminado exitosamente"
     *     )
     * )
     */
    public function index(): Response
    {
        $customers = $this->repository->getAllCustomers();

        return $this->render('index.html.twig', ['customers' => $customers]);
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
        $form = $this->formGenerator()
            ->handleRequest($request);

        if($request->getContent()){
            $customer = $form->getData();

            $this->repository->save($customer);

            return $this->redirectToRoute('api.index');
        }

        return $this->render('create.html.twig', ['form' => $form->createView()]);
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
        $form = $this->formGenerator($id)
            ->handleRequest($request);

        if($request->getContent()){
            $customer = $form->getData();

            $this->repository->save($customer);

            return $this->redirectToRoute('api.index');
        }

        return $this->render('update.html.twig', ['form' => $form->createView()]);
    }

    /**
     * 
     * Delete an existing customer
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
     *     )
     * )
     */
    public function delete(int $id): Response
    {

        $this->repository->delete($id);

        return $this->redirectToRoute('api.index');
    }

    private function formGenerator(int $id = null): FormInterface
    {
        $customer = new Customer();

        if($id) {
            $customer = $this->repository->getCustomer($id);
        }

        return $this->createFormBuilder($customer)
            ->add('name', TextType::class, array('attr' =>array('class' => 'form-control mb-0', 'required')))
            ->add('address', TextType::class, array('attr' =>array('class' => 'form-control', 'required')))
            ->add('province', TextType::class, array('attr' =>array('class' => 'form-control', 'required')))
            ->add('cif', TextType::class, array('attr' =>array('class' => 'form-control', 'required')))
            ->add('save', SubmitType::class, array('label' =>'Aceptar','attr' =>array('class'=>'btn btn-primary mt-3')))
            ->getForm();
    }

}
