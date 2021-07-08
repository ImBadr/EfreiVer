<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use App\Entity\Conversation;
use App\Entity\User;
use App\Entity\Message;
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Encoder\NixillaJWTEncoder;

class MessageController extends AbstractController
{
    /**
     * @Route("/api/message", name="create", methods="POST")
     */
    public function create(Request $request, NixillaJWTEncoder $jwtManager, SerializerInterface $serializer): Response
    {
        try {
            if($request->headers->get('Authorization') == null) throw new \Exception("Missing header");
            $jwt = $jwtManager->decode(explode(" ", $request->headers->get('Authorization'))[1]);
            $userId = $jwt["id"];

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($userId);

            $senderId = $request->request->get("sender");
            $conversationId = $request->request->get("conversation");
            $text = $request->request->get("text");

            $sender = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($senderId);

            $conversation = $this->getDoctrine()
                ->getRepository(Conversation::class)
                ->find($conversationId);

            if(!$sender || !$conversation) {
                return new JsonResponse(["error" => true, "message" => "bad parameters"]);
            }

            $em = $this->getDoctrine()->getManager();

            $message = new Message();
            $message->setSender($sender);
            $message->setConversation($conversation);
            $message->setContent($text);
            $message->setSeen(false);
            $em->persist($message);
            $em->flush();

            $serializer = $serializer->serialize($message, 'json', SerializationContext::create()->setGroups(['message']));
            return new JsonResponse(json_decode($serializer));
        } catch (\Exception $e){
            return new JsonResponse(["error" => true, "message" => $e->getMessage() ]);
        }
    }

    /**
     * @Route("/api/message/{id}/seen", name="seen", methods="GET")
     */
    public function seend(Request $request, int $id, NixillaJWTEncoder $jwtManager, SerializerInterface $serializer): Response
    {
        try {
            if($request->headers->get('Authorization') == null) throw new \Exception("Missing header");
            $jwt = $jwtManager->decode(explode(" ", $request->headers->get('Authorization'))[1]);
            $userId = $jwt["id"];

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($userId);

            $message = $this->getDoctrine()
                ->getRepository(Message::class)
                ->find($id);

            if(!$message) {
                return new JsonResponse(["error" => true, "message" => "Message doesn't not exist"]);
            }

            $em = $this->getDoctrine()->getManager();
            $message->setSeen(true);
            $em->persist($message);
            $em->flush();

            $serializer = $serializer->serialize($message, 'json', SerializationContext::create()->setGroups(['message']));
            return new JsonResponse(json_decode($serializer));
        } catch (\Exception $e){
            return new JsonResponse(["error" => true, "message" => $e->getMessage() ]);
        }
    }
}
