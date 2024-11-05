<?php

namespace Api\Controller;

use DateInterval;
use Api\Entity\User;
use Api\Form\UserType;
use Api\Traits\ResponseTrait;
use Doctrine\ORM\EntityManagerInterface;
use Api\Service\CsvUploadService;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Api\Service\DatabaseService;

class UserController extends AbstractFOSRestController
{
    /**
     * This trait is used to return response from the controller,
     * which will serialize the response object into json format
     */
    use ResponseTrait;

   /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var PasswordHasherFactoryInterface
     */
    private PasswordHasherFactoryInterface $hasherFactory;

    /**
     * @var ValidatorInterface $validator
     */
    private ValidatorInterface $validator;
    
    private DatabaseService $databaseService;


    public function __construct(DatabaseService $databaseService,EntityManagerInterface $entityManager, PasswordHasherFactoryInterface $hasherFactory, ValidatorInterface $validator)
    {
        $this->em = $entityManager;
        $this->hasherFactory = $hasherFactory;
        $this->validator = $validator;
        $this->databaseService = $databaseService;
    }

    #[Route('/api/users/list', name: 'users.index', methods: ['GET'])]
    /**
     * List of Users
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        // select required fields
        $select = 'u.id , u.name, u.email, u.roles, u.status, u.createdAt, u.updatedAt';
        // Fetch users
        $users =  $this->em->getRepository(User::class)->findAllQuery([], $select)->getQuery()->getResult();
        return $this->responseWithData($users);
    }

    #[Route('/api/users/register', name: 'users.create', methods: ["POST"])]
    /**
     * Register a new User
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function create(Request $request): JsonResponse|Response
    {

        //Logged User Id
        $userId = $request->get('user')['userId'];

        // Decode Json content int associated array
        $data = json_decode($request->getContent(), true);

        //updated data with created by and updated by
        $data['createdBy'] = $userId;
        $data['updatedBy'] = $userId;

        // Hashing Password using hasher Factory
        $passwordHasher = $this->hasherFactory->getPasswordHasher(User::class);
        $hashPassword = $passwordHasher->hash($data['password']);

        if($this->em->getRepository(User::class)->findOneBy(['email' => $data['email']]))
        {
            return $this->errorWithData('User Already Exist.', ['email' => ['This email is already in use.']], 'Email Validation');
        }

        // replace plain password with hash password
        $data['password'] = $hashPassword;

        $form = $this->createForm(UserType::class, new User());
        $form->submit($data);

        if($form->isSubmitted()){

            $user = $form->getData();

            //if the form is valid
            if(!$form->isValid()){

                $errors = $this->getErrorsFromForm($form);
                return $this->errorWithData('There was a validation error', $errors, 'validation_error', 400);
            }

            // Persist and flush user into database by using doctrine
            $this->em->persist($user);
            $this->em->flush();
        }

        return $this->responseWithData(['message' => 'Successfully user created.']);

    }
    
    
    #[Route('/api/users/logins', name: 'users.logins', methods: ["POST"])]
    public function login( Request $request): JsonResponse
    {
        
        $data = json_decode($request->getContent(), true);
     ;
        if (!isset($data['username'], $data['password'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Username and password are required.'
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $data['username']]);

        if (!$user) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid username or password.'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        $passwordHasher = $this->hasherFactory->getPasswordHasher(User::class);

        if (!$passwordHasher->verify($user->getPassword(), $data['password'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Invalid username or password.'
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }
       
       $jws = $this->jwtTokenManager->create($user);
           

        $token = $jws->getToken();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Login successful',
            'token' => $token,
        ], JsonResponse::HTTP_OK);
    }
    /**
     * @Route("/api/users/upload", name="api_upload_users", methods={"POST"})
     */
    public function uploadUsers(Request $request, CsvUploadService $csvUploadService): JsonResponse
    {
        $file = $request->files->get('file');

        if (!$file || $file->getClientOriginalExtension() !== 'csv') {
            return new JsonResponse(['error' => 'Please upload a valid CSV file.'], 400);
        }

        try {
            $result = $csvUploadService->uploadAndStore($file);
            return new JsonResponse(['message' => $result], 200);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @Route("/api/users/backup", name="api_backup", methods={"GET"})
     */
    public function backupDatabase(): JsonResponse
    {
        try {
            $backupFilePath = $this->databaseService->backupDatabase();
            return new JsonResponse([
                'message' => 'Database backup successful',
                'file' => $backupFilePath
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
 * @Route("/api/users/restore", name="api_restore", methods={"POST"})
 */
public function restoreDatabase(): JsonResponse
{
    try {
        $this->databaseService->restoreDatabase();
        return new JsonResponse(['message' => 'Database restored successfully']);
    } catch (\Exception $e) {
        return new JsonResponse(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
   
}
