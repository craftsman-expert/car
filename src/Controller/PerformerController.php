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
 * Class PerformerController
 * @package App\Controller
 * @Route("/performers")
 */
class PerformerController extends AbstractController
{
    private EntityManagerInterface $em;
    private PerformerRepository $performerRepository;
    private NormalizerInterface $normalizer;

    /**
     * AudioRecordController constructor.
     * @param EntityManagerInterface $em
     * @param NormalizerInterface $normalizer
     */
    public function __construct(EntityManagerInterface $em, NormalizerInterface $normalizer)
    {
        $this->em = $em;
        $this->normalizer = $normalizer;
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
        $performers = $this->performerRepository->search(
            $request->get("q", ""),
            $request->get("offset", 0),
            $request->get("count", 100),
        );

        $performersNormalized = $this->normalizer->normalize($performers, "json", [
            AbstractNormalizer::ATTRIBUTES => [
                "id",
                "name"
            ]
        ]);

        return $this->json($performersNormalized);
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
        $performer = $this->performerRepository->find($id);

        if (!$performer instanceof AudioRecord) {
            throw new NotFoundException("Audio file not found", "not_found");
        }

        $performerNormalized = $this->normalizer->normalize($performer, "json", [
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

        $path = $performerNormalized["path"];
        $performerNormalized["src"] = $_ENV["AUDIO_SERVER"] . $path;
        unset($performerNormalized["path"]);

        return $this->json($performerNormalized);
    }

    /**
     * @Route(
     *     path="/{id}",
     *     methods={"GET"}
     * )
     * @param int $id
     * @param Request $request
     * @return JsonResponse
     * @throws NotFoundException
     */
    public function edit(int $id, Request $request)
    {
        $performer = $this->performerRepository->find($id);

        if (!$performer instanceof Performer) {
            throw new NotFoundException("Performer not found", "not_found");
        }

        if ($request->request->has("name"))

        return $this->json($performerNormalized);
    }

    /**
     * @Route(
     *     path="",
     *     methods={"POST"}
     * )
     * @param Request $request
     * @return JsonResponse
     * @throws ExceptionInterface
     */
    public function add(Request $request)
    {
        $performer = new Performer();
        $performer->setName($request->get("name"));

        $this->em->persist($performer);
        $this->em->flush();

        $performerNormalized = $this->normalizer->normalize($performer, "json", [
            AbstractNormalizer::ATTRIBUTES => [
                "id",
                "name",
            ]
        ]);

        return $this->json($performerNormalized, Response::HTTP_CREATED);
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
        $performer = $this->performerRepository->find($id);

        if (!$performer instanceof AudioRecord) {
            throw new NotFoundException("Audio file not found", "not_found");
        }

        $this->em->remove($performer);
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
     * @throws ExceptionInterface
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

        $performers = [];
        /** @var UploadedFile $file */
        foreach ($files as $file) {
            $path = $fileUploader->upload($file);
            $performer = new AudioRecord();
            $performer->setName($file->getClientOriginalName());
            $performer->setPath($path);

            $this->em->persist($performer);

            $performers[] = $performer;
        }

        $this->em->flush();

        $performersNormalized = $this->normalizer->normalize($performers, "json", [
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

        return $this->json($performersNormalized, Response::HTTP_CREATED);

    }
}
