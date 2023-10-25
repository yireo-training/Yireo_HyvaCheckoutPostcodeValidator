<?php declare(strict_types=1);

namespace Yireo\HyvaCheckoutPostcodeValidator\Controller\Ajax;

use Exception;
use InvalidArgumentException;
use Magento\Directory\Model\Country\Postcode\ValidatorInterface;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\CsrfAwareActionInterface;
use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Controller\Result\JsonFactory as ResultJsonFactory;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\Phrase;
use Magento\Framework\Serialize\SerializerInterface;

class Postcode implements HttpPostActionInterface, CsrfAwareActionInterface
{
    public function __construct(
        private Request $request,
        private SerializerInterface $serializer,
        private ResultJsonFactory $resultJsonFactory,
        private ValidatorInterface $validator
    ) {
    }

    public function execute()
    {
        $body = $this->request->getContent();
        $data = $this->serializer->unserialize($body);

        $postcode = (string)$data['postcode'];
        $countryId = (string)$data['countryId'];

        if (empty($postcode)) {
            return $this->sendResult(false, __('Empty postcode'));
        }

        if (empty($countryId)) {
            return $this->sendResult(false, __('Empty country ID'));
        }

        try {
            if (false === $this->validator->validate($postcode, $countryId)) {
                $msg = 'Invalid postcode "'.$postcode.'" for country "'.$countryId.'"';

                return $this->sendResult(false, __($msg));
            }
        } catch (InvalidArgumentException $exception) {
            return $this->sendResult(false, __('No validation possible for this country'));
        } catch (Exception $exception) {
            return $this->sendResult(false, __(get_class($exception).': '.$exception->getMessage()));
        }

        return $this->sendResult(true, __('Valid postcode'));
    }

    public function createCsrfValidationException(RequestInterface $request): ?InvalidRequestException
    {
        return null;
    }

    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }

    /**
     * @param bool $valid
     * @param Phrase $message
     * @return ResultJson
     */
    private function sendResult(bool $valid, Phrase $message): ResultJson
    {
        $resultJson = $this->resultJsonFactory->create();

        return $resultJson->setData([
            'valid' => (int)$valid,
            'message' => (string)$message,
        ]);
    }
}
