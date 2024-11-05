<?php


namespace Api\Traits;


use Doctrine\Common\Annotations\AnnotationReader;
use FOS\RestBundle\Context\Context;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


trait ResponseTrait
{
    private $logger;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param null $message
     * @param int $status
     * @return JsonResponse
     *
     * Only return error message with response code
     */
    protected function error($message = null, int $status =  Response::HTTP_BAD_REQUEST): JsonResponse
    {
        // prepare response
        $response = [
            "status" => "error",
            "message" => $message
        ];

        // return json response
        return new JsonResponse($response, $status);
    }

    /**
     * @param string $message
     * @param array $data
     * @param string $type
     * @param int $status
     * @return JsonResponse
     *
     * return error response with data
     */
    public function errorWithData(string $message = '', array $data = [], string $type = '', int $status = Response::HTTP_BAD_REQUEST): JsonResponse
    {
        // prepare for response with data having status code
        $response = [
            'status' => 'error',
            'type' => $type,
            'message' => $message,
            'errors' => $data
        ];

        // return json response
        return new JsonResponse($response, $status);
    }

    public function successWithData(string $message = '', array $data = []): JsonResponse
    {
        // prepare for response with data having status code
        $response = [
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ];

        // return json response
        return new JsonResponse($response);
    }

    /**
     * @param string | null $message
     * @param int $status
     * @return JsonResponse
     */
    protected function success(string $message = null, int $status = Response::HTTP_OK): JsonResponse
    {
        // prepare response
        $response = [
            "code" => $status,
            "status" => "success",
            "message" => $message
        ];

        // return json response
        return new JsonResponse($response);
    }

    /**
     * Generate a response with the provided data.
     *
     * @param mixed|null $data The data to be included in the response.
     * @param int $responseCode The HTTP response code.
     * @param array $type The response type (content-type).
     * @return Response The generated response.
     */
    protected function responseWithData(mixed $data = null, int $responseCode = Response::HTTP_OK, array $type = []): Response
    {
        // Create a view with the data, response code, and type
        $view = $this->view($data, $responseCode, $type);

        // Configure the context to serialize null values
        $context = new Context();
        $context->setSerializeNull(true);
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @param $message
     * @param array $context
     * @param \Throwable|null $throwable
     * @return void
     */
    public function handleLog($message, array $context = [], \Throwable $throwable = null): void
    {
        if ($throwable) {
            $context['exception'] = $throwable;

            $this->logger->error($message, $context);
        } else {
            $this->logger->info($message, $context);
        }
    }


    /**
     * @param $object
     * @param array $select
     * @param array $ignore
     * @param array $callbacks
     * @param string $message
     * @return Response
     *
     * This function for the symfony serialization, convert object into json format
     * Its accepts callback for format any values, ignore for ignore any attributes and message
     */
    public function handelCustomSerializer($object, array $select = [], array $ignore = [], array $callbacks = [], string $message = 'OK'): Response
    {
        // default callbacks for handling data time
        $defaultCallbacks = [
            'createdAt' => function ($dateTime) {
                return $dateTime?  $dateTime->format('M d, Y h:i A') : null;
            },
            'updatedAt' => function ($dateTime) {
                return $dateTime?  $dateTime->format('M d, Y h:i A') : null;
            },
        ];

        // merge default callbacks and custom callbacks
        $allCallbacks = array_merge($defaultCallbacks, $callbacks);

        // Create a class metadata factory using annotation loader
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        // Prepare the response format with status, code, message, and data
        $format = [
            "status" => "success",
            "code" => Response::HTTP_OK,
            "message" => $message,
            "data" => $object
        ];

        // Set up encoders (XML and JSON) and normalizers (ObjectNormalizer with class metadata)
        $encoder = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoder);

        // Serialize the response format using JSON format, with additional options
        $data = $serializer->serialize($format, 'json', [
            AbstractNormalizer::IGNORED_ATTRIBUTES => $ignore,
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::CALLBACKS => $allCallbacks,
        ]);

        // return json response
        return new Response($data,Response::HTTP_OK, ['Content-Type' => 'application/json;charset=UTF-8']);
    }

    /**
     * @param FormInterface $form
     * @return array
     *
     * This function gets the errors of From Interface and return
     * into array format
     * with name of entity attribute with their respective errors
     */
    private function getErrorsFromForm(FormInterface $form): array
    {

        $errors = array();
        // get error messages
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        // get all forms child and verify child with FormInterface and gets error and store in error array
        foreach ($form->all() as $childForm) {

            if ($childForm instanceof FormInterface)
            {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        // return errors into array
        return $errors;
    }
}