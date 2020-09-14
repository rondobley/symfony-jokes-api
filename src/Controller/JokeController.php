<?php

namespace App\Controller;

use App\Entity\Joke;
use App\Repository\JokeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Jokes API", version="1.0.0")
 */
class JokeController extends AbstractController
{
    /**
     * @var JokeRepository
     */
    private $jokeRepository;

    /**
     * JokeController constructor.
     *
     * @param JokeRepository $jokeRepository
     */
    public function __construct(JokeRepository $jokeRepository) {
        $this->jokeRepository = $jokeRepository;
    }

    /**
     * @Route("/jokes", name="create_joke", methods={"POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param ValidatorInterface $validator
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     *
     * @OA\Post(
     *     path="/jokes",
     *     summary="Create joke",
     *     description="Add a new joke to the collection",
     *     operationId="create_joke",
     *     @OA\RequestBody(
     *         description="JSON object with a joke",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="joke",
     *                     type="string"
     *                 ),
     *                 example={"joke": "This is funny"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Joke created"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function create(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        try {
            $data = json_decode($request->getContent(), true);
            if ($data != null) {
                // If we have valid JSON make sure we have a valid Joke
                $joke = new Joke();
                $joke->setJoke($data['joke']);

                $errors = $validator->validate($joke);
                if (count($errors) > 0) {
                    return $this->returnError($this->getErrorMessages($errors), Response::HTTP_BAD_REQUEST);
                }

                $entityManager->persist($joke);
                $entityManager->flush();

                return $this->json($this->getSuccessResponseData('Joke created'), Response::HTTP_CREATED);
            }
        } catch (\Exception $e) {

            return $this->returnError(array($e->getMessage()));
        }
    }

    /**
     * getAll
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/jokes", name="get_all_jokes", methods={"GET"})
     *
     * @OA\Get(
     *     path="/jokes",
     *     summary="Get Jokes",
     *     description="Gets a paginated list of jokes",
     *     operationId="get_all_jokes",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number to retrieve",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The jokes"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function getAll(Request $request)
    {
        try {
            $data = $this->jokeRepository->getAll($request->query->getInt('page', 1));

            return $this->json($data, Response::HTTP_OK);

        } catch (\Exception $e) {

            return $this->returnError(array($e->getMessage()));
        }
    }

    /**
     * getById
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/jokes/{id}", name="get_a_joke", methods={"GET"}, requirements={"id"="\d+"})
     *
     * @OA\Get(
     *     path="/jokes/{id}",
     *     summary="Get a joke",
     *     description="Gets a joke",
     *     operationId="get_a_joke",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The id of the joke",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The joke"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Joke not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function getById($id)
    {
        try {
            $joke = $this->jokeRepository->find((int)$id);
            if ($joke !== null) {
                return $this->json($joke->toArray(), Response::HTTP_OK);
            }

            return $this->returnError(array("Joke not found"), Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {

            return $this->returnError(array($e->getMessage()));
        }
    }

    /**
     * getRandom
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/jokes/random", name="get_a_random_joke", methods={"GET"})
     *
     * @OA\Get(
     *     path="/jokes/random",
     *     summary="Get a randome joke",
     *     description="Gets a random joke",
     *     operationId="get_a_random_joke",
     *     @OA\Response(
     *         response=200,
     *         description="A random joke"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No joke found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function getRandom()
    {
        try {
            $joke = $this->jokeRepository->getRandom();
            if ($joke !== null) {
                return $this->json($joke, Response::HTTP_OK);
            }

            // This should not happen, but there could possibly be zero jokes in the DB
            return $this->returnError(array("No joke found"), Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {

            return $this->returnError(array($e->getMessage()));
        }
    }

    /**
     * Update
     *
     * @param int $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/jokes/{id}", name="update_a_joke", methods={"PUT"}, requirements={"id"="\d+"})
     *
     * @OA\Put(
     *     path="/jokes/{id}",
     *     summary="Update joke",
     *     description="Update a  joke in the collection",
     *     operationId="update_a_joke",
     *     @OA\RequestBody(
     *         description="JSON object with a joke",
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="joke",
     *                     type="string"
     *                 ),
     *                 example={"joke": "This is funnier"}
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The id of the joke",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Joke updated"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Joke not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function update($id, Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        try {
            $data = json_decode($request->getContent(), true);
            $joke = $entityManager->getRepository(Joke::class)->find($id);

            if (!$joke) {
                return $this->returnError(array("Joke not found"), Response::HTTP_NOT_FOUND);
            }

            $joke->setJoke($data['joke']);

            $errors = $validator->validate($joke);
            if (count($errors) > 0) {
                return $this->returnError($this->getErrorMessages($errors), Response::HTTP_BAD_REQUEST);
            }

            $entityManager->flush();

            return $this->json($this->getSuccessResponseData('Joke updated'), Response::HTTP_OK);

        } catch (\Exception $e) {

            return $this->returnError(array($e->getMessage()));
        }
    }

    /**
     * @Route("/jokes/{id}", name="delete_joke", methods={"DELETE"})
     */
    /**
     * delete
     *
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     * @Route("/jokes/{id}", name="delete_joke", methods={"DELETE"})
     *
     * @OA\Delete(
     *     path="/jokes/{id}",
     *     summary="Delete a joke",
     *     description="Deletes a joke",
     *     operationId="delete_joke",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The id of the joke",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Joke deleted"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Joke not found"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function delete($id, EntityManagerInterface $entityManager)
    {
        try {
            $joke = $entityManager->getRepository(Joke::class)->find($id);

            if (!$joke) {
                return $this->returnError(array("Joke not found"), Response::HTTP_NOT_FOUND);
            }

            $entityManager->remove($joke);
            $entityManager->flush();

            return $this->json($this->getSuccessResponseData('Joke updated'), Response::HTTP_NO_CONTENT);

        } catch (\Exception $e) {

            return $this->returnError(array($e->getMessage()));
        }
    }

    /**
     * getSuccessResponseData
     *
     * A helper to set success return bodies
     *
     * @param $message
     * @return array
     */
    private function getSuccessResponseData($message)
    {
        return array('Success' => true, 'Message' => $message);
    }

    /**
     * getErrorMessages
     *
     * @param \Symfony\Component\Validator\ConstraintViolationList $violations
     * @return array
     */
    private function getErrorMessages(\Symfony\Component\Validator\ConstraintViolationList $violations)
    {
        $errors = array();
        foreach ($violations->getIterator() as $error) {
            $errors[] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * returnError
     *
     * A helper to return error responses
     *
     * @param array $errors
     * @param int $status
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function returnError(array $errors, int $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->json(array('Success' => false, 'Errors' => $errors), $status);
    }
}
