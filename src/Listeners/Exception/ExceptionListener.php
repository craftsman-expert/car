<?php


namespace App\Listeners\Exception;


use App\Exception\HTTP\IHTTPStatus;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Throwable;

/**
 * Class ExceptionListener
 * @package App\Listeners\Exception
 */
class ExceptionListener implements EventSubscriberInterface
{
    private array $headers = [];

    /**
     * ExceptionListener constructor.
     */
    public function __construct()
    {
        $this->headers = array_merge($this->headers, [
            "Access-Control-Allow-Origin" => "*"
        ]);
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * ['eventName' => 'methodName']
     *  * ['eventName' => ['methodName', $priority]]
     *  * ['eventName' => [['methodName1', $priority], ['methodName2']]]
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException'],
        ];
    }


    /**
     * @param ExceptionEvent $event
     * @return JsonResponse|void
     */
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        do {

            // API HTTP STATUS
            if ($exception instanceof IHTTPStatus) {
                return $this->handleHttpStatus($event, $exception);
            }

            // HTTP NOT FOUND
            if ($exception instanceof NotFoundHttpException) {
                $this->handleHttpNotFound($event, $exception);
                return;
            }

            $this->handleException($event, $exception);

        } while (null !== $exception = $exception->getPrevious());
    }


    /**
     * @param ExceptionEvent $event
     * @param Throwable $exception
     */
    private function handleArgumentResolverException(ExceptionEvent $event, Throwable $exception): void
    {
        $request = [
            "http_method" => $event->getRequest()->getMethod(),
            "request_method" => $event->getRequest()->getPathInfo(),
            "parameters" => $event->getRequest()->request->all()
        ];

        $response = [
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "request" => $request
        ];

        if ($_SERVER['APP_ENV'] == "dev") {
            $response = array_merge($response, [
                "message_debug" => $exception->getMessage(),
                "stack_trace" => $exception->getTrace()
            ]);
        }

        $er = new JsonResponse($response);
        $er->send();
    }


    /**
     * @param ExceptionEvent $event
     * @param Throwable $exception
     * @return void
     */
    private function handleException(ExceptionEvent $event, Throwable $exception): void
    {
        $for_dev = [];

        if ($_SERVER['APP_ENV'] == "dev") {
            $for_dev = [
                "debug_message" => $exception->getMessage()
            ];
        }
        $jr = new JsonResponse(array_merge([
            "error_code" => "unknown_error",
            "error_message" => "Неизвестная ошибка",
        ],$for_dev), 520, $this->headers);

        $jr->send();
    }

    /**
     * @param ExceptionEvent $event
     * @param Throwable $exception
     */
    private function handleHttpNotFound(ExceptionEvent $event, Throwable $exception): void
    {
        $jr = new JsonResponse([
            "error_code" => "http_not_found",
            "error_message" => "Передан неизвестный метод",
        ], Response::HTTP_NOT_FOUND, $this->headers);

        $jr->send();
    }


    /**
     * @param ExceptionEvent $event
     * @param IHTTPStatus $exception
     * @return JsonResponse
     */
    private function handleHttpStatus(ExceptionEvent $event, IHTTPStatus $exception)
    {
        $jr = new JsonResponse([
            "error_code" => $exception->getErrorCode(),
            "error_message" => $exception->getErrorMessage(),
        ], $exception->getHttpStatusCode(), $this->headers);
        return $jr->send();
    }
}
