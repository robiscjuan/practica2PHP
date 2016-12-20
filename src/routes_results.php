<?php // apiResultsDoctrine - src/routes_results.php

use MiW16\Results\Entity\Result;
use Swagger\Annotations as SWG;

/**
 * Summary: Returns all results
 * Notes: Returns all results from the system that the result has access to.
 *
 * @SWG\Get(
 *     method      = "GET",
 *     path        = "/results",
 *     tags        = { "Results" },
 *     summary     = "Returns all results",
 *     description = "Returns all results from the system that the result has access to.",
 *     operationId = "miw_cget_results",
 *     @SWG\Response(
 *          response    = 200,
 *          description = "Results array response",
 *          schema      = { "$ref": "#/definitions/ResultsArray" }
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          description = "Results object not found",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 * @var \Slim\App $app
 */
$app->get(
    '/results',
    function ($request, $response, $args) {
        $this->logger->info('GET \'/results\'');
        $results = getEntityManager()
            ->getRepository('MiW16\Results\Entity\Result')
            ->findAll();

        if (empty($results)) { // 404 - Results object not found
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'Results object not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        }

        return $response->withJson(array('results' => $results));
    }
)->setName('miw_cget_results');

/**
 * Summary: Returns a result based on a single ID
 * Notes: Returns the result identified by &#x60;resultId&#x60;.
 *
 * @SWG\Get(
 *     method      = "GET",
 *     path        = "/results/{resultId}",
 *     tags        = { "Results" },
 *     summary     = "Returns a result based on a single ID",
 *     description = "Returns the result identified by `resultId`.",
 *     operationId = "miw_get_results",
 *     parameters  = {
 *          { "$ref" = "#/parameters/resultId" }
 *     },
 *     @SWG\Response(
 *          response    = 200,
 *          description = "Results",
 *          schema      = { "$ref": "#/definitions/Results" }
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          description = "Results id. not found",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 */
$app->get(
    '/results/{id:[0-9]+}',
    function ($request, $response, $args) {
        $this->logger->info('GET \'/results/' . $args['id'] . '\'');
        $results = getEntityManager()
            ->getRepository('MiW16\Results\Entity\Result')
            ->findOneById($args['id']);

        if (empty($results)) {  // 404 - Results id. not found
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'Results not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        }

        return $response->withJson($results);
    }
)->setName('miw_get_results');

/**
 * Summary: Deletes a result
 * Notes: Deletes the result identified by &#x60;resultId&#x60;.
 *
 * @SWG\Delete(
 *     method      = "DELETE",
 *     path        = "/results/{resultId}",
 *     tags        = { "Results" },
 *     summary     = "Deletes a result",
 *     description = "Deletes the result identified `resultId`.",
 *     operationId = "miw_delete_results",
 *     parameters={
 *          { "$ref" = "#/parameters/resultId" }
 *     },
 *     @SWG\Response(
 *          response    = 204,
 *          description = "Results deleted &lt;Response body is empty&gt;"
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          description = "Results not found",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 */
$app->delete(
    '/results/{id:[0-9]+}',
    function ($request, $response, $args) {
        $this->logger->info('DELETE \'/results/' . $args['id'] . '\'');
        $em = getEntityManager();
        $result = $em
            ->getRepository('MiW16\Results\Entity\Result')
            ->findOneById($args['id']);
        if (empty($result)) {  // 404 - Results id. not found
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'Results not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        } else {
            $em->remove($result);
            $em->flush();
        }

        return $response->withStatus(204);
    }
)->setName('miw_delete_results');

/**
 * Summary: Provides the list of HTTP supported methods
 * Notes: Return a &#x60;Allow&#x60; header with a list of HTTP supported methods.
 *
 * @SWG\Options(
 *     method      = "OPTIONS",
 *     path        = "/results",
 *     tags        = { "Results" },
 *     summary     = "Provides the list of HTTP supported methods",
 *     description = "Return a `Allow` header with a list of HTTP supported methods.",
 *     operationId = "miw_options_results",
 *     @SWG\Response(
 *          response    = 200,
 *          description = "`Allow` header &lt;Response body is empty&gt;",
 *     )
 * )
 */
$app->options(
    '/results',
    function ($request, $response, $args) {
        $this->logger->info('OPTIONS \'/results\'');

        return $response
            ->withHeader(
                'Allow',
                'OPTIONS, GET, POST, PUT, DELETE'
            );
    }
)->setName('miw_options_results');

/**
 * Summary: Creates a new result
 * Notes: Creates a new result
 *
 * @SWG\Post(
 *     method      = "POST",
 *     path        = "/results",
 *     tags        = { "Results" },
 *     summary     = "Creates a new result",
 *     description = "Creates a new result",
 *     operationId = "miw_post_results",
 *     parameters  = {
 *          {
 *          "name":        "data",
 *          "in":          "body",
 *          "description": "`Result` properties to add to the system",
 *          "required":    true,
 *          "schema":      { "$ref": "#/definitions/ResultData" }
 *          }
 *     },
 *     @SWG\Response(
 *          response    = 201,
 *          description = "`Created` Result created",
 *          schema      = { "$ref": "#/definitions/Result" }
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          description = "Result not found",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 */
$app->post(
    '/results',
    function ($request, $response, $args) {
        $this->logger->info('POST \'/results\'');

        $entityManager = getEntityManager();
        $data = json_decode($request->getBody(), true);
        if (!isset($data['result']))
            return 'Result is required';

        $result = $data['result'];

        if (isset($data['time']))
            try {
                $time = new DateTime($data['time']);
            } catch (Exception $e) {
                //Si la fecha es incorrecta se pone la actual
                $time = new DateTime();
            }
        else
            $time = new DateTime();

        $userRepository = $entityManager->getRepository('MiW16\Results\Entity\User');

        $user = $userRepository->findOneById($data['user_id']);
        if ($user === null) {
            http_response_code(404);
            return 'User not found';
        }

        $result = new Result($result, $user, $time);

        $entityManager->persist($result);
        $entityManager->flush();

        $response->withStatus(201);
        return $response->withJson($result);
    }
)->setName('miw_post_results');

/**
 * Summary: Updates a result
 * Notes: Updates the result identified by &#x60;resultId&#x60;.
 *
 * @SWG\Put(
 *     method      = "PUT",
 *     path        = "/results/{resultId}",
 *     tags        = { "Results" },
 *     summary     = "Updates a result",
 *     description = "Updates the result identified by `resultId`.",
 *     operationId = "miw_put_results",
 *     parameters={
 *          { "$ref" = "#/parameters/resultId" },
 *          {
 *          "name":        "data",
 *          "in":          "body",
 *          "description": "`Results` data to update",
 *          "required":    true,
 *          "schema":      { "$ref": "#/definitions/ResultData" }
 *          }
 *     },
 *     @SWG\Response(
 *          response    = 200,
 *          description = "`Ok` Result previously existed and is now updated",
 *          schema      = { "$ref": "#/definitions/Result" }
 *     ),
 *     @SWG\Response(
 *          response    = 400,
 *          description = "`Bad Request` Wrong user id",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          description = "`Not Found` The result could not be found",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 */
$app->put(
    '/results/{id:[0-9]+}',
    function ($request, $response, $args) {
        $this->logger->info('PUT \'/results\'');

        $entityManager = getEntityManager();
        $resultRepository = $entityManager->getRepository('MiW16\Results\Entity\Result');
        $data = json_decode($request->getBody(), true); // parse the JSON into an assoc. array

        /** @var Results $result */
        $result = $resultRepository->findOneById($args['id']);
        if ($result === null) {
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'Result not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        } else {

            if (isset($data['resultname'])) {
                $resultname = $data['resultname'];

                if ($resultRepository->findOneByResultsname($resultname) !== null) {
                    $newResponse = $response->withStatus(400);
                    $datos = array(
                        'code' => 400,
                        'message' => 'Resultsname in use'
                    );
                    return $this->renderer->render($newResponse, 'message.phtml', $datos);

                }
                $result->setResultsname($resultname);
            }

            if (isset($data['user_id'])) {
                if ($data['user_id'] != 0) {
                    $userRepository = $entityManager->getRepository('MiW16\Results\Entity\User');
                    $user = $userRepository->findOneById($data['user_id']);
                    if ($user === null) {
                        $newResponse = $response->withStatus(404);
                        $datos = array(
                            'code' => 400,
                            'message' => 'Wrong user id'
                        );
                        return $this->renderer->render($newResponse, 'message.phtml', $datos);
                    }
                    $result->setUser($user);
                }
            }

            if (isset($data['result'])) {

                if (!empty($data['result'])) {
                    $result->setResult($data['result']);
                }
            }

            if (isset($data['time'])) {
                try {
                    $time = new DateTime($data['time']);
                    $result->setTime($time);
                } catch (Exception $e) {
                    //Fecha incorrecta, no se actualiza
                }
            }

            $entityManager->flush();

            return $response->withJson($result);
        }
    }
)->setName('miw_post_results');

