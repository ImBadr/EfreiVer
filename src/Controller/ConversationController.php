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
use Symfony\Component\HttpFoundation\Request;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use App\Encoder\NixillaJWTEncoder;

class ConversationController extends AbstractController
{

    /**
     * @Route("/conversation", name="conversation", methods="GET")
     */
    public function index(SerializerInterface $serializer, NixillaJWTEncoder $jwt): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $token = $jwt->encode(["id" => $user->getId()]);
        $conversations = $this->getDoctrine()
                ->getRepository(Conversation::class)
                ->getConversations($user);
        
        return $this->render('conversation/index.html.twig', ["id"=> $user->getId(), "jwt" => $token, "conversations" => $serializer = $serializer->serialize($conversations, 'json', SerializationContext::create()->setGroups(['messagerie']))]);
    }

    /**
     * @Route("/api/conversations", name="conversations", methods="GET")
     */
    public function conversations(Request $request, NixillaJWTEncoder $jwtManager, SerializerInterface $serializer): Response
    {
        try {
            if($request->headers->get('Authorization') == null) throw new \Exception("Missing header");
            $jwt = $jwtManager->decode(explode(" ", $request->headers->get('Authorization'))[1]);
            $userId = $jwt["id"];

            $user = $this->getDoctrine()
                ->getRepository(User::class)
                ->find($userId);

           $conversations = $this->getDoctrine()
                ->getRepository(Conversation::class)
                ->getConversations($user);

            $serializer = $serializer->serialize($conversations, 'json', SerializationContext::create()->setGroups(['messagerie']));
            return new JsonResponse(json_decode($serializer));
        } catch (\Exception $e){
            return new JsonResponse(["error" => true, "message" => $e->getMessage() ]);
        }
    }

    /**
     * @Route("/api/conversation/{id}", name="conversation_view", methods="GET")
     */
    public function conversation(Request $request, int $id, NixillaJWTEncoder $jwtManager, SerializerInterface $serializer): Response
    {
        $jwt = $jwtManager->decode(explode(" ", $request->headers->get('Authorization'))[1]);
        $userId = $jwt["id"];

        $conversation = $this->getDoctrine()
            ->getRepository(Conversation::class)
            ->find($id);

        $serializer = $serializer->serialize($conversation, 'json', SerializationContext::create()->setGroups(['messagerie']));
        return new JsonResponse(json_decode($serializer));
    }

    /**
     * @Route("/start/{id}", name="conversation_create", methods="GET")
     */
    public function go(int $id, Request $request, SerializerInterface $serializer) {
        $sender = $this->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $receiver = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

        if(!$sender || !$receiver) {
            return new JsonResponse(["error" => "error"]);
        }

        $conversation = $this->getDoctrine()
            ->getRepository(Conversation::class)
            ->hasConversation($sender, $receiver);

        if(count($conversation) == 0){
            $conversation = new Conversation();
            $conversation->setSender($sender);
            $conversation->setReceiver($receiver);
            $em->persist($conversation);
            $em->flush();
        }
        
        return $this->redirectToRoute('conversation');
    }
}
