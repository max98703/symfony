<?php

namespace Api\Service;

use Doctrine\ORM\EntityManagerInterface;
use Api\Entity\User;
use Api\Form\UserType;
use Api\Traits\ResponseTrait;
use Api\Service\EmailService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class CsvUploadService
{
    use ResponseTrait;

    private EntityManagerInterface $em;
    private FormFactoryInterface $formFactory;
    
    private EmailService $emailService;
    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->em = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function uploadAndStore(UploadedFile $file , EmailService $emailService): string
    {
        if (($handle = fopen($file->getPathname(), 'r')) === false) {
            throw new \Exception("Could not open the file.");
        }

        $countRow = 0;

        while (($row = fgetcsv($handle, 1000, ',')) !== false) {
            $countRow++;

            if ($countRow === 1) {
                continue; // Skip header row
            }

            // Map CSV row data to User entity fields
            $data = [
                'name' => trim($row[0]),
                'email' => trim($row[1]),
                'username' => trim($row[2]),
                'address' => trim($row[3]),
                'role' => trim($row[4])
            ];

            // Create and submit the form
            $form = $this->formFactory->create(UserType::class, new User());
            $form->submit($data);

            // Check if form is valid
            if (!$form->isValid()) {
                $errors = $this->getErrorsFromForm($form);
                return $this->errorWithData('There was a validation error', $errors, 'validation_error', 400);
            }

            // Persist valid user data to the database
            $user = $form->getData();
            $this->em->persist($user);

            // Send notification email
            $emailService->sendEmail($user, 'successfully stored', 'file upload');
        }

        // Flush all changes at once
        $this->em->flush();
        fclose($handle);

        return "CSV data has been processed and users have been notified.";
    }

}
