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
     * @Route("/add/{username}", name="newConversations")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function add(Request $request, $username)
    {
        //$otherUser = $request->get('otherUser', 0);

        // $otherUser =     $request->query->get('id',0);
        $otherUser = $this->userRepository->findBy(['username' => $username])[0];


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
            return $this->redirectToRoute('index.chat');

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


        return $this->redirectToRoute('index.chat');
    }


    /**
     * @Route("/", name="getConversations", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function getConvs(Request $request)
    {
        $conversations = $this->conversationRepository->findConversationsByUser($this->getUser()->getId());

        $hubUrl = $this->getParameter('mercure.default_hub');

        $request->headers->set('Content-Type', 'text/plain');

        $this->addLink($request, new Link('mercure', $hubUrl));

        return $this->json($conversations);
    }


    /**
     * @Route("/delete/{username}", name="delete", methods={"POST"})
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request, $username)
    {

        //$otherUser =     $request->query->get('username',0);
        $otherUser = $this->userRepository->findBy(['username' => $username])[0];
        $user = $this->getUser();

        $conversation = $this->conversationRepository->findConversationsByUser(
            $otherUser->getId()

        );

        foreach ($conversation as $conv) {
            if ($conv['username'] === $user->getUsername()) {
                $convToDelete = $this->conversationRepository->findBy(['id' => $conv['conversationId']]);
                $this->entityManager->remove($convToDelete[0]);
                $this->entityManager->flush();
                return new Response("-1");
            }
        }

    }

}
