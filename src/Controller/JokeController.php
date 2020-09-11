<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Jokes API", version="1.0.0")
 */
class JokeController extends AbstractController
{
    /**
     * @var \App\Repository\JokeRepository
     */
    private $jokeRepository;

    /**
     * JokeController constructor.
     *
     * @param \App\Repository\JokeRepository $jokeRepository
     */
    public function __construct(\App\Repository\JokeRepository $jokeRepository)
    {
        $this->jokeRepository = $jokeRepository;
    }

    /**
     * @Route("/jokes", name="create_joke", methods={"POST"})
     * @param Request $request
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
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function create(Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            if ($data != null) {
                // If we have valid JSON make sure we have a joke param, and if we do make sure it is text
                if (empty($data['joke'])) {
                    return $this->returnError('Must specify a joke');
                } elseif (!is_string($data['joke'])) {
                    return $this->returnError('Joke must only contain text');
                }
            }

            $this->jokeRepository->saveJoke($data['joke']);

            return $this->json($this->getSuccessResponseData('Joke created'), Response::HTTP_CREATED);

        } catch (\Exception $e) {

            return $this->returnError($e->getMessage());
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

            return $this->returnError($e->getMessage());
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

            return $this->returnError("Joke not found", Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {

            return $this->returnError($e->getMessage());
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
     *     OA\Response(
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
            return $this->returnError("No joke found", Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {

            return $this->returnError($e->getMessage());
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
     *         response=404,
     *         description="Could not update joke"
     *     ),
     *     OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function update($id, Request $request)
    {
        try {
            $data = json_decode($request->getContent(), true);
            if ($data != null) {
                // If we have valid JSON make sure we have a joke param, and if we do make sure it is text
                if (empty($data['joke'])) {
                    return $this->returnError('Must specify a joke');
                } elseif (!is_string($data['joke'])) {
                    return $this->returnError('Joke must only contain text');
                }
            }
            $joke = $this->jokeRepository->find((int)$id);
            if ($joke !== null) {
                $joke->setJoke($data['joke']);
                $updatedJoke = $this->jokeRepository->updateJoke($joke);

                return $this->json($updatedJoke->toArray(), Response::HTTP_OK);
            }

            return $this->returnError("Joke not found", Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {

            return $this->returnError($e->getMessage());
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
     *     OA\Response(
     *         response=500,
     *         description="Internal Server Error"
     *     )
     * )
     */
    public function delete($id)
    {
        try {
            $joke = $this->jokeRepository->find((int)$id);
            if ($joke !== null) {
                $this->jokeRepository->deleteJoke($joke);

                return $this->json($this->getSuccessResponseData('Joke deleted'), Response::HTTP_NO_CONTENT);
            }

            return $this->returnError("Joke not found", Response::HTTP_NOT_FOUND);

        } catch (\Exception $e) {

            return $this->returnError($e->getMessage());
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
     * returnError
     *
     * A helper to return error responses
     *
     * @param string $message
     * @param int $status
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    private function returnError(string $message, int $status = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        return $this->json(array('Success' => false, 'Message' => $message), Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}
