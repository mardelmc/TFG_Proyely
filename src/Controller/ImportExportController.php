<?php

namespace App\Controller;

use App\Entity\AcademicYear;
use App\Entity\Group;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Repository\AcademicYearRepository;
use App\Repository\GroupRepository;
use App\Repository\StudentRepository;
use App\Repository\TeacherRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ImportExportController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(ManagerRegistry $registry, LoggerInterface $logger, UserPasswordHasherInterface $passwordHasher)
    {
        $this->logger = $logger;
        $this->passwordHasher = $passwordHasher;
    }

    #[Route('/importStudent', name: 'importStudent')]
    final public function importStudent(
        Request $request,
        GroupRepository $groupRepository,
        StudentRepository $studentRepository,
        AcademicYearRepository $academicYearRepository,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $uploadedFile = $request->files->get('csvFile');

            if ($uploadedFile) {
                try {
                    // Leer el contenido del archivo con codificación Windows-1252
                    $filePath = $uploadedFile->getPathname();
                    $fileContent = file_get_contents($filePath);
                    $fileContentUtf8 = mb_convert_encoding($fileContent, 'UTF-8', 'Windows-1252');

                    // Crear el lector de CSV desde el contenido convertido
                    $csv = \League\Csv\Reader::createFromString($fileContentUtf8);
                    $csv->setHeaderOffset(0); 

                    // Normalizar los encabezados
                    $headers = $csv->getHeader();
                    $normalizedHeaders = array_map('strtolower', $headers);

                    // Verificar encabezados necesarios
                    if (!in_array('unidad', $normalizedHeaders) || !in_array('fecha de matrícula', $normalizedHeaders)) {
                        throw new \Exception('El archivo CSV no contiene las columnas requeridas.');
                    }

                    $records = $csv->getRecords(); // Obtener registros como iterador
                    $newStudents = 0;
                    $updatedStudents = 0;

                    foreach ($records as $record) {
                        // Datos del registro
                        $unitName = $record['Unidad'] ?? null;
                        $unit = $record['Curso'] ?? null;
                        $enrollmentDateRaw = $record['Fecha de matrícula'] ?? null;

                        // Validación de unidad y fecha de matrícula
                        if (!$unitName || !$enrollmentDateRaw) {
                            continue;
                        }

                        // Procesar la fecha de matrícula
                        $enrollmentDate = \DateTime::createFromFormat('d/m/Y', $enrollmentDateRaw);
                        if (!$enrollmentDate) {
                            continue;
                        }

                        // Calcular el año académico
                        $academicYearStart = (int)$enrollmentDate->format('Y'); // Convertimos a entero
                        $academicYearEnd = $academicYearStart + 1;             // Calculamos el siguiente año
                        $academicYearDescription = "Curso" . $academicYearStart . '-' . $academicYearEnd;
                        $academicYear = $academicYearRepository->findOneBy(['description' => $academicYearDescription]);
                        if (!$academicYear) {
                            $academicYear = new AcademicYear();
                            $academicYear->setDescription($academicYearDescription);
                            $academicYear->setStartDate(new \DateTime($academicYearStart . '-09-15'));
                            $academicYear->setEndDate(new \DateTime($academicYearEnd . '-06-30'));

                            $entityManager->persist($academicYear);
                        }

                        $this->logger->info("Buscando grupo", ['Grupos' => $unitName]);
                        // Buscar o crear el grupo
                        $group = $groupRepository->findOneBy(['name' => $unitName]);
                        $this->logger->info("Grupo encontrado", ['Grupo' =>$group]);
                        if (!$group) {
                            $group = new Group();
                            $group->setName($unitName);
                            $group->setDescription($unit);
                            $group->setAcademicYear($academicYear);
                            $groupRepository->add($group);
                            $groupRepository->save();
                        }

                        $studentDni = $record['DNI/Pasaporte'] ?? null;
                        $existingStudent = $studentRepository->findOneBy(['nickname' => $studentDni]);

                        if (!$existingStudent ) {
                            // Crear nuevo estudiante
                            $student = new Student();

                            $student->setFirstName($record['Nombre'] ?? 'Desconocido');
                            $student->setLastName(($record['Primer apellido'] ?? '') . ' ' . ($record['Segundo apellido'] ?? ''));
                            $student->setGroup($group);
                            $student->setNickname($studentDni);
                            $hashedPassword = $this->passwordHasher->hashPassword($student, "1234");
                            $student->setPassword($hashedPassword);
                            $entityManager->persist($student);
                            $this->logger->info("added student: ", ['student'=>$student->getId()]);
                        } else {
                            // Actualizar información del estudiante existente
                            $existingStudent->setGroup($group);
                        }
                        $updatedStudents++;
                    }

                    // Persistir los cambios en la base de datos
                    $entityManager->flush();

                    $this->addFlash('success', "Importación completada: $newStudents nuevos, $updatedStudents actualizados.");
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                    $this->addFlash('error', 'Hubo un error al procesar el archivo CSV: ' . $e->getMessage());
                }
            } else {
                $this->addFlash('error', 'No se subió ningún archivo.');
            }
        }

        return $this->render('import/student.html.twig');
    }


    #[Route('/importTeacher', name: 'importTeacher')]
    final public function importTeacher(
        Request $request,
        GroupRepository $groupRepository,
        TeacherRepository $teacherRepository,
        AcademicYearRepository $academicYearRepository,
        EntityManagerInterface $entityManager
    ): Response {
        if ($request->isMethod('POST')) {
            $uploadedFile = $request->files->get('csvFile');

            if ($uploadedFile) {
                try {
                    // Leer el contenido del archivo con codificación Windows-1252
                    $filePath = $uploadedFile->getPathname();
                    $fileContent = file_get_contents($filePath);
                    $fileContentUtf8 = mb_convert_encoding($fileContent, 'UTF-8', 'Windows-1252');

                    // Crear el lector de CSV desde el contenido convertido
                    $csv = \League\Csv\Reader::createFromString($fileContentUtf8);
                    $csv->setHeaderOffset(0);

                    // Normalizar los encabezados
                    $headers = $csv->getHeader();
                    $normalizedHeaders = array_map('strtolower', $headers);

                    // Verificar encabezados necesarios
                    if (!in_array('usuario idea', $normalizedHeaders) || !in_array('nombre', $normalizedHeaders)) {
                        throw new \Exception('El archivo CSV no contiene las columnas requeridas.');
                    }

                    $records = $csv->getRecords(); // Obtener registros como iterador
                    $newTeachers = 0;
                    $updatedTeachers = 0;

                    foreach ($records as $record) {
                        // Datos del registro
                        $userIdEA = $record['Usuario IdEA'] ?? null;
                        $firstName = $record['Nombre'] ?? null;
                        $lastName = trim(($record['Apellido1'] ?? '') . ' ' . ($record['Apellido2'] ?? ''));

                        // Validación de datos
                        if (!$userIdEA || !$firstName || !$lastName) {
                            continue;
                        }

                        // Buscar o crear el profesor
                        $existingTeacher = $teacherRepository->findOneBy(['nickname' => $userIdEA]);

                        if (!$existingTeacher) {
                            // Crear nuevo profesor
                            $teacher = new Teacher();
                            $teacher->setFirstName($firstName);
                            $teacher->setLastName($lastName);
                            $teacher->setNickname($userIdEA);
                            $hashedPassword = $this->passwordHasher->hashPassword($teacher, "defaultPassword");
                            $teacher->setPassword($hashedPassword);

                            $entityManager->persist($teacher);
                            $newTeachers++;
                        } else {
                            // Actualizar información del profesor existente
                            $existingTeacher->setFirstName($firstName);
                            $existingTeacher->setLastName($lastName);
                            $updatedTeachers++;
                        }
                    }

                    // Persistir los cambios en la base de datos
                    $entityManager->flush();

                    $this->addFlash('success', "Importación completada: $newTeachers nuevos, $updatedTeachers actualizados.");
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                    $this->addFlash('error', 'Hubo un error al procesar el archivo CSV: ' . $e->getMessage());
                }
            } else {
                $this->addFlash('error', 'No se subió ningún archivo.');
            }
        }

        return $this->render('import/teacher.html.twig');
    }
}