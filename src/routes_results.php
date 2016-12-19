<?php // apiResultsDoctrine - src/routes_results.php

use MiW16\Results\Entity\Results;
use Swagger\Annotations as SWG;

/**
 * Summary: Returns all results
 * Notes: Returns all results from the system that the user has access to.
 *
 * @SWG\Get(
 *     method      = "GET",
 *     path        = "/results",
 *     tags        = { "Results" },
 *     summary     = "Returns all results",
 *     description = "Returns all results from the system that the user has access to.",
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
        $usuarios = getEntityManager()
            ->getRepository('MiW16\Results\Entity\Results')
            ->findAll();

        if (empty($usuarios)) { // 404 - Results object not found
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'Results object not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        }

        return $response->withJson(array('results' => $usuarios));
    }
)->setName('miw_cget_results');

/**
 * Summary: Returns a user based on a single ID
 * Notes: Returns the user identified by &#x60;userId&#x60;.
 *
 * @SWG\Get(
 *     method      = "GET",
 *     path        = "/results/{userId}",
 *     tags        = { "Results" },
 *     summary     = "Returns a user based on a single ID",
 *     description = "Returns the user identified by `userId`.",
 *     operationId = "miw_get_results",
 *     parameters  = {
 *          { "$ref" = "#/parameters/userId" }
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
        $usuario = getEntityManager()
            ->getRepository('MiW16\Results\Entity\Results')
            ->findOneById($args['id']);

        if (empty($usuario)) {  // 404 - Results id. not found
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'Results not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        }

        return $response->withJson($usuario);
    }
)->setName('miw_get_results');

/**
 * Summary: Deletes a user
 * Notes: Deletes the user identified by &#x60;userId&#x60;.
 *
 * @SWG\Delete(
 *     method      = "DELETE",
 *     path        = "/results/{userId}",
 *     tags        = { "Results" },
 *     summary     = "Deletes a user",
 *     description = "Deletes the user identified `userId`.",
 *     operationId = "miw_delete_results",
 *     parameters={
 *          { "$ref" = "#/parameters/userId" }
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
        $usuario = $em
            ->getRepository('MiW16\Results\Entity\Results')
            ->findOneById($args['id']);
        if (empty($usuario)) {  // 404 - Results id. not found
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'Results not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        } else {
            $em->remove($usuario);
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
 * Summary: Creates a new user
 * Notes: Creates a new user
 *
 * @SWG\Post(
 *     method      = "POST",
 *     path        = "/results",
 *     tags        = { "Results" },
 *     summary     = "Creates a new user",
 *     description = "Creates a new user",
 *     operationId = "miw_post_results",
 *     parameters  = {
 *          {
 *          "name":        "data",
 *          "in":          "body",
 *          "description": "`Results` properties to add to the system",
 *          "required":    true,
 *          "schema":      { "$ref": "#/definitions/ResultsData" }
 *          }
 *     },
 *     @SWG\Response(
 *          response    = 201,
 *          description = "`Created` Results created",
 *          schema      = { "$ref": "#/definitions/Results" }
 *     ),
 *     @SWG\Response(
 *          response    = 400,
 *          description = "`Bad Request` Resultsname or email already exists.",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     ),
 *     @SWG\Response(
 *          response    = 422,
 *          description = "`Unprocessable entity` Resultsname, e-mail or password is left out",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 */
$app->post(
    '/results',
    function ($request, $response, $args) {
        $this->logger->info('POST \'/results\'');

        $entityManager = getEntityManager();
        $userRepository = $entityManager->getRepository('MiW16\Results\Entity\Results');
        $data = json_decode($request->getBody(), true);
        if (!isset($data['username']) || !isset($data['email']) || !isset($data['password']) || !isset($data['enabled']))
            return 'username , email, password and enabled are required';

        $username = $data['username'];
        $email = $data['email'];
        $password = $data['password'];
        $enabled = $data['enabled'];

        if ($userRepository->findOneByResultsname($username)) {
            $newResponse = $response->withStatus(400);
            $datos = array(
                'code' => 400,
                'message' => 'Resultsname in use'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        }
        if ($userRepository->findOneByEmail($email)) {
            $newResponse = $response->withStatus(400);
            $datos = array(
                'code' => 400,
                'message' => 'Email in use'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        }


        $user = new Results();
        $user->setResultsname($username);
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setEnabled($enabled);

        $entityManager->persist($user);
        $entityManager->flush();

        $response->withStatus(201);
        return $response->withJson($user);
    }
)->setName('miw_post_results');

/**
 * Summary: Updates a user
 * Notes: Updates the user identified by &#x60;userId&#x60;.
 *
 * @SWG\Put(
 *     method      = "PUT",
 *     path        = "/results/{userId}",
 *     tags        = { "Results" },
 *     summary     = "Updates a user",
 *     description = "Updates the user identified by `userId`.",
 *     operationId = "miw_put_results",
 *     parameters={
 *          { "$ref" = "#/parameters/userId" },
 *          {
 *          "name":        "data",
 *          "in":          "body",
 *          "description": "`Results` data to update",
 *          "required":    true,
 *          "schema":      { "$ref": "#/definitions/ResultsData" }
 *          }
 *     },
 *     @SWG\Response(
 *          response    = 200,
 *          description = "`Ok` Results previously existed and is now updated",
 *          schema      = { "$ref": "#/definitions/Results" }
 *     ),
 *     @SWG\Response(
 *          response    = 400,
 *          description = "`Bad Request` Results name or e-mail already exists",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     ),
 *     @SWG\Response(
 *          response    = 404,
 *          description = "`Not Found` The user could not be found",
 *          schema      = { "$ref": "#/definitions/Message" }
 *     )
 * )
 */
$app->put(
    '/results/{id:[0-9]+}',
    function ($request, $response, $args) {
        $this->logger->info('PUT \'/results\'');

        $entityManager = getEntityManager();
        $userRepository = $entityManager->getRepository('MiW16\Results\Entity\Results');
        $data = json_decode($request->getBody(), true); // parse the JSON into an assoc. array

        /** @var Results $user */
        $user = $userRepository->findOneById($args['id']);
        if ($user === null) {
            $newResponse = $response->withStatus(404);
            $datos = array(
                'code' => 404,
                'message' => 'Results not found'
            );
            return $this->renderer->render($newResponse, 'message.phtml', $datos);
        } else {

            if (isset($data['username'])) {
                $username = $data['username'];

                if ($userRepository->findOneByResultsname($username) !== null) {
                    $newResponse = $response->withStatus(400);
                    $datos = array(
                        'code' => 400,
                        'message' => 'Resultsname in use'
                    );
                    return $this->renderer->render($newResponse, 'message.phtml', $datos);

                }
                $user->setResultsname($username);
            }

            if (isset($data['email'])) {

                $email = $data['email'];
                if ($userRepository->findOneByEmail($email) !== null) {
                    $newResponse = $response->withStatus(400);
                    $datos = array(
                        'code' => 400,
                        'message' => 'Resultsname in use'
                    );
                    return $this->renderer->render($newResponse, 'message.phtml', $datos);
                }
                $user->setEmail($email);
            }

            if (isset($data['password'])) {

                $user->setPassword($data['password']);
            }

            if (isset($data['enabled'])) {
                $enabled = $data['enabled'];
                $user->setEnabled($enabled);
            }

            $entityManager->flush();

            return $response->withJson($user);
        }
    }
)->setName('miw_post_results');

