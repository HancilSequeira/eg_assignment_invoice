<?php


namespace App\Service;

use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    /**
     * @var Logger
     */
    private $logger;
    private $serializer;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(LoggerInterface $logger, ValidatorInterface $validator)
    {
        $this->logger = $logger;
        $this->validator = $validator;
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function validateRequestPayload(Request $request, $DTOClass, $format = "json")
    {
        try {
            $jsonRequest = $request->getContent();
            $requestArray = json_decode($jsonRequest, true);

            $requestDtoObj = $this->serializer->deserialize($request->getContent(), $DTOClass, $format);
            if ($requestDtoObj->getErrors() !== '') {
                return ["code" => Response::HTTP_BAD_REQUEST, $requestDtoObj->getErrors()];
            }

            $errors = $this->validator->validate($requestDtoObj);
            $error = $errorMessage = [];
            if (count($errors) > 0) {
                foreach ($errors as $key => $violation) {
                    $errorMessage[$key]['code'] = '';
                    $errorMessage[$key]['field'] = $violation->getPropertyPath();
                    $errorMessage[$key]['message'] = $violation->getMessage();
                }
                $error['code'] = Response::HTTP_BAD_REQUEST;
                $error['description'] = "Input request data is not valid";
                $error['error'] = $errorMessage;
            }

        } catch (\Exception $ex) {
            $this->logger->error($ex->getMessage());
            throw new \Exception($ex->getMessage());
        }
    }
}