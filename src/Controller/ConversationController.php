<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Participant;
use App\Repository\ConversationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\WebLink\Link;

/**
 * @Route("/conversations", name="conversations.")
 */
class ConversationController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ConversationRepository
     */
    private $conversationRepository;

    public function __construct(UserRepository $userRepository,
                                EntityManagerInterface $entityManager,
ConversationRepository $conversationRepository)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->conversationRepository = $conversationRepository;
    }

    /**
     * @Route("/", name="newConversations", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        //$otherUser = $request->get('otherUser', 0);

        $otherUser =     $request->query->get('id',0);
        $otherUser = $this->userRepository->find($otherUser);

        if (is_null($otherUser)) {
            throw new \Exception("The user was not found");
        }

        // cannot create a conversation with myself

        if ($otherUser->getId() === $this->getUser()->getId()) {
            throw new \Exception("That's deep but you cannot create a conversation with yourself");
        }


        // Check if conversation already exists
        $conversation = $this->conversationRepository->findConversationByParticipants(
            $otherUser->getId(),
            $this->getUser()->getId()
        );

        if (count($conversation)) {
            throw new \Exception("The conversation already exists");
        }

        $conversation = new Conversation();

        $participant = new Participant();
        $participant->setUser($this->getUser());
        $participant->setConversation($conversation);


        $otherParticipant = new Participant();
        $otherParticipant->setUser($otherUser);
        $otherParticipant->setConversation($conversation);

        $this->entityManager->getConnection()->beginTransaction();
        try {
            $this->entityManager->persist($conversation);
            $this->entityManager->persist($participant);
            $this->entityManager->persist($otherParticipant);

            $this->entityManager->flush();
            $this->entityManager->commit();

        } catch (\Exception $e) {
            $this->entityManager->rollback();
            throw $e;
        }


        return $this->json([
            'id' => $conversation->getId()
        ], Response::HTTP_CREATED, [], []);
    }


    /**
     * @Route("/", name="getConversations", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getConvs(Request $request) {
        $conversations = $this->conversationRepository->findConversationsByUser($this->getUser()->getId());
        
        $hubUrl = $this->getParameter('mercure.default_hub');

        $request->headers->set('Content-Type', 'ok mdr');

        $this->addLink($request, new Link('mercure', $hubUrl));

        return $this->json($conversations);
    }

    /**
     * @Route("/add", name="add", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request) {

        dd();
        $this->redirectToRoute('conversations.newConversations',['']);
    }

    /**
     * @Route("/delete", name="delete", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request) {

        $otherUser =     $request->query->get('id',0);
        $otherUser = $this->userRepository->find($otherUser);
        $user = $this->getUser();

        $conversation = $this->conversationRepository->findConversationsByUser(
            $otherUser->getId()

        );

        foreach ($conversation as $conv){
            if($conv['username'] === $user->getUsername()){
                $convToDelete = $this->conversationRepository->findBy(['id'=>$conv['conversationId']]);
                $this->entityManager->remove($convToDelete[0]);
                $this->entityManager->flush();
            }
        }

    }

}
