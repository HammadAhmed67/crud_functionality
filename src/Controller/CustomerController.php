<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CustomerController extends AbstractController
{
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }
    #[Route('/customer/add', name: 'add_customer',methods:'POST')]
    public function add(Request $request): JsonResponse 
    {
        $data = json_decode($request->getContent(),true);
        $firstName = $data['firstName'];
        $lastName = $data['lastName'];
        $email = $data ['email'];
        $phoneNumber = $data['phoneNumber'];

        if(empty($firstName) || empty($lastName) || empty($email) || empty($phoneNumber)){
            throw new NotFoundHttpException('Expecting mandatory parameters!');
        }
        $this->customerRepository->saveCustomer($firstName, $lastName, $email, $phoneNumber);

        return new JsonResponse(['status' => 'Customer created!'], Response::HTTP_CREATED);
    }

    #[Route('/customer/{id}', name: 'get_one_customer',methods:'GET')]
    public function get($id): JsonResponse
    {
        $customer = $this->customerRepository->findOneBy(['id' => $id]);

        $data = [
            'id' => $customer->getId(),
            'firstName' => $customer->getFirstName(),
            'lastName' => $customer->getLastName(),
            'email' => $customer->getEmail(),
            'phoneNumber' => $customer->getPhoneNumber(),
        ];

        return new JsonResponse($data, Response::HTTP_OK);
    }

    #[Route('/customers', name: 'get_all_customers',methods:'GET')]
    public function get_all() : JsonResponse
    {
        $customers = $this->customerRepository->findAll();
        $data = [];
        foreach($customers as $customer){
            $data[] = [
                'id' => $customer->getId(),
                'firstName' => $customer->getFirstName(),
                'lastName' => $customer->getLastName(),
                'email' => $customer->getEmail(),
                'phoneNumber' => $customer->getPhoneNumber(),
            ];
           
        }
        return new JsonResponse($data,Response::HTTP_OK);
    }

    #[Route('/customer/update/{id}', name: 'get_all_customer',methods:'PUT')]
    public function update(Request $request, $id): JsonResponse 
    {
        $customer = $this->customerRepository->findOneBy(['id' => $id]);
        $data = json_decode($request->getContent(),true);

        empty($data['firstName']) ? true : $customer->setFirstName($data['firstName']);
        empty($data['lastName']) ? true : $customer->setLastName($data['lastName']);
        empty($data['email']) ? true : $customer->setEmail($data['email']);
        empty($data['phoneNumber']) ? true : $customer->setPhoneNumber($data['phoneNumber']);
        
        $updatedCustomer = $this->customerRepository->update_customer($customer);
        return new JsonResponse($updatedCustomer->toArray(),Response::HTTP_OK);


    }
    #[Route('/customer/delete/{id}', name: 'delete_customer',methods:'DELETE')]
    public function delete($id):JsonResponse
    {
        $customer = $this->customerRepository->findOneBy(['id'=>$id]);

        $this->customerRepository->remove($customer);
        return new JsonResponse(['status' => 'Customer Deleted!'],Response::HTTP_OK);

    
    }
}
