<?php

namespace App\Controller;

use App\Entity\AudioRecord;
use App\Entity\Performer;
use App\Exception\Http\BadRequestException;
use App\Exception\Http\NotFoundException;
use App\Repository\AudioRecordRepository;
use App\Repository\PerformerRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * Class AudioRecordController
 * @package App\Controller
 * @Route("/audio")
 */
class AudioRecordController extends AbstractController
{
    private EntityManagerInterface $em;
    private AudioRecordRepository $audioRecordRepository;
    private NormalizerInterface $normalizer;
    private PerformerRepository $performerRepository;

    /**
     * AudioRecordController constructor.
     * @param EntityManagerInterface $em
     * @param NormalizerInterface $normalizer
     */
    public function __construct(EntityManagerInterface $em, NormalizerInterface $normalizer)
    {
        $this->em = $em;
        $this->normalizer = $normalizer;
        $this->audioRecordRepository =$em->getRepository(AudioRecord::class);
        $this->performerRepository = $em->getRepository(Performer::class);
    }

    /**
     * @Route(
     *     path="/search",
     *     methods={"GET"}
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function search(Request $request)
    {
        $audioRecords = $this->audioRecordRepository->search(
            $request->get("q", ""),
            $request->get("q", "")
        );

        $audioRecordsNormalized = $this->normalizer->normalize($audioRecords, "json", [
            AbstractNormalizer::ATTRIBUTES => [
                "id",
                "name",
                "performer" => [
                    "id",
                    "name"
                ],
                "path",
                "like",
            ]
        ]);

        foreach ($audioRecordsNormalized as $index => $item) {
            $path = $audioRecordsNormalized[$index]["path"];
            $audioRecordsNormalized[$index]["src"] = $_ENV["AUDIO_SERVER"] . $path;

            unset($audioRecordsNormalized[$index]["path"]);
        }

        return $this->json($audioRecordsNormalized);
    }

    /**
     * @Route(
     *     path="/{id}",
     *     methods={"GET"}
     * )
     * @param int $id
     * @return JsonResponse
     * @throws ExceptionInterface
     * @throws NotFoundException
     */
    public function getById(int $id)
    {
        $audioRecord = $this->audioRecordRepository->find($id);

        if (!$audioRecord instanceof AudioRecord) {
            throw new NotFoundException("Audio file not found", "not_found");
        }

        $audioRecordNormalized = $this->normalizer->normalize($audioRecord, "json", [
            AbstractNormalizer::ATTRIBUTES => [
                "id",
                "name",
                "performer" => [
                    "id",
                    "name"
                ],
                "path",
                "like",
            ]
        ]);

        $path = $audioRecordNormalized["path"];
        $audioRecordNormalized["src"] = $_ENV["AUDIO_SERVER"] . $path;
        unset($audioRecordNormalized["path"]);

        return $this->json($audioRecordNormalized);
    }

    /**
     * @Route(
     *     path="/{id}",
     *     methods={"PATCH"}
     * )
     * @param int $id
     * @param Request $request
     * @return Response
     * @throws NotFoundException
     */
    public function edit(int $id, Request $request)
    {
        $audioRecord = $this->audioRecordRepository->find($id);

        if (!$audioRecord instanceof AudioRecord) {
            throw new NotFoundException("Audio record not found", "not_found");
        }

        $audioRecord->setName($request->get("name", $audioRecord->getName()));

        if ($request->request->has("performer_id")) {
            $performer = $this->performerRepository->find($request->get("performer_id"));
            if ($performer instanceof Performer) {
                $audioRecord->setPerformer($performer);
            }
        }

        $this->em->flush();

        return new Response("", Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     path="/like/{id}",
     *     methods={"GET"}
     * )
     * @param int $id
     * @return Response
     * @throws NotFoundException
     */
    public function like(int $id)
    {
        $audioRecord = $this->audioRecordRepository->find($id);

        if (!$audioRecord instanceof AudioRecord) {
            throw new NotFoundException("Audio file not found", "not_found");
        }

        $audioRecord->setLike(true);
        $this->em->flush();

        return new Response("", Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     path="/not-like/{id}",
     *     methods={"GET"}
     * )
     * @param int $id
     * @return Response
     * @throws NotFoundException
     */
    public function notLike(int $id)
    {
        $audioRecord = $this->audioRecordRepository->find($id);

        if (!$audioRecord instanceof AudioRecord) {
            throw new NotFoundException("Audio file not found", "not_found");
        }

        $audioRecord->setLike(false);
        $this->em->flush();

        return new Response("", Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     path="/{id}",
     *     methods={"DELETE"}
     * )
     * @param int $id
     * @return Response
     * @throws NotFoundException
     */
    public function delete(int $id)
    {
        $audioRecord = $this->audioRecordRepository->find($id);

        if (!$audioRecord instanceof AudioRecord) {
            throw new NotFoundException("Audio file not found", "not_found");
        }

        $this->em->remove($audioRecord);
        $this->em->flush();

        return new Response("", Response::HTTP_NO_CONTENT);
    }

    /**
     * @Route(
     *     path="/upload",
     *     methods={"POST"}
     * )
     * @param Request $request
     * @param FileUploader $fileUploader
     * @return JsonResponse
     */
    public function upload(Request $request, FileUploader $fileUploader)
    {
        $files = $request->files->all();

        // Oops!
        $relativePath = sprintf("/%s/%s/%s/",
            date('Y'),
            date('H'),
            date('I'),
        );
        $fileUploader->setRelativePath($relativePath);

        $audioRecords = [];
        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $path = $fileUploader->upload($file);
            $audioRecord = new AudioRecord();
            $audioRecord->setName($file->getClientOriginalName());
            $audioRecord->setPath($path);

            $this->em->persist($audioRecord);

            $audioRecords[] = $audioRecord;
        }

        $this->em->flush();

        $audioRecordsNormalized = $this->normalizer->normalize($audioRecords, "json", [
            AbstractNormalizer::ATTRIBUTES => [
                "id",
                "name",
                "performer" => [
                    "id",
                    "name"
                ],
                "path",
                "like",
            ]
        ]);

        return $this->json($audioRecordsNormalized, Response::HTTP_CREATED);

    }
}
