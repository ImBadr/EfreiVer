<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use App\Encoder\NixillaJWTEncoder;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;

class TokenController extends AbstractController
{
    /**
     * @Route("/api/check", name="check_token", methods="GET")
     */
    public function index(Request $request, SerializerInterface $serializer, NixillaJWTEncoder $jwtManager): Response
    {
        try {
            if($request->headers->get('Authorization') == null) throw new \Exception("Missing header");
            $token = explode(" ", $request->headers->get('Authorization'))[1];
            $jwt = $jwtManager->decode($token);
            $userId = $jwt["id"];

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($userId);

            $serializer = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups(['messagerie']));
            $data = json_decode($serializer);
            $data->token = $token;
            return new JsonResponse(["user" => $data]);
        } catch (\Exception $e){
            return new JsonResponse(["error" => true, "message" => "Forbidden"]);
        }
    }
}
