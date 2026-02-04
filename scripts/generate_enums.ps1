$root = 'D:\My Data\Buy&Bye\B&B_API\BuyAndByeApi\src\Domain\Shared\Enums'
New-Item -ItemType Directory -Path $root -Force | Out-Null

$enums = @(
  @{ Name = 'UserRole'; Type = 'int'; Values = @('Buyer = 1', 'Seller = 2', 'Admin = 3') },
  @{ Name = 'AccountStatus'; Type = 'string'; Values = @('PendingVerification', 'Active', 'Suspended', 'Banned', 'Deleted', 'Incomplete', 'Skipped') },
  @{ Name = 'ConditionGrade'; Type = 'string'; Values = @('New', 'LikeNew', 'VeryGood', 'Good', 'Acceptable') },
  @{ Name = 'ProductStatus'; Type = 'string'; Values = @('Draft', 'Active', 'OutOfStock', 'Archived', 'Banned') },
  @{ Name = 'AuthErrorCode'; Type = 'string'; Values = @('InvalidCredentials', 'AccountDisabled', 'TokenExpired', 'TokenInvalid') },
  @{ Name = 'CurrencyCode'; Type = 'string'; Values = @('USD', 'EUR', 'GBP') },
  @{ Name = 'DiscountType'; Type = 'string'; Values = @('Percentage', 'FixedAmount') },
  @{ Name = 'DeliveryMethod'; Type = 'string'; Values = @('Shipping', 'Pickup', 'Digital') },
  @{ Name = 'NotificationType'; Type = 'string'; Values = @('System', 'Order', 'Message', 'Promotion') },
  @{ Name = 'ConversationType'; Type = 'string'; Values = @('Direct', 'Group') },
  @{ Name = 'MessageType'; Type = 'string'; Values = @('Text', 'Image', 'System') },
  @{ Name = 'AddressType'; Type = 'string'; Values = @('Billing', 'Shipping') },
  @{ Name = 'PaymentProvider'; Type = 'string'; Values = @('Stripe', 'Paypal', 'BankTransfer') },
  @{ Name = 'PaymentStatus'; Type = 'string'; Values = @('Pending', 'Authorized', 'Captured', 'Failed', 'Refunded') },
  @{ Name = 'PayoutStatus'; Type = 'string'; Values = @('Pending', 'InTransit', 'Paid', 'Failed') },
  @{ Name = 'OrderStatus'; Type = 'string'; Values = @('Draft', 'Placed', 'Paid', 'Shipped', 'Delivered', 'Cancelled', 'Refunded') },
  @{ Name = 'StreamVisibility'; Type = 'string'; Values = @('Public', 'Unlisted', 'Private') },
  @{ Name = 'StreamStatus'; Type = 'string'; Values = @('Scheduled', 'Live', 'Ended', 'Cancelled') },
  @{ Name = 'ReportStatus'; Type = 'string'; Values = @('Open', 'InReview', 'Resolved', 'Rejected') },
  @{ Name = 'ShippingCarrier'; Type = 'string'; Values = @('DHL', 'UPS', 'FedEx', 'USPS', 'Other') },
  @{ Name = 'SellerVerificationStatus'; Type = 'string'; Values = @('NotSubmitted', 'Pending', 'Approved', 'Rejected') },
  @{ Name = 'ReportReason'; Type = 'string'; Values = @('Fraud', 'Counterfeit', 'Harassment', 'Spam', 'Other') }
)

foreach ($enum in $enums) {
  $lines = @()
  $lines += '<?php'
  $lines += ''
  $lines += 'declare(strict_types=1);'
  $lines += ''
  $lines += 'namespace Src\Domain\Shared\Enums;'
  $lines += ''
  $lines += ('enum ' + $enum.Name + ': ' + $enum.Type)
  $lines += '{'
  foreach ($value in $enum.Values) {
    if ($enum.Type -eq 'int') {
      $lines += ('    case ' + $value + ';')
    } else {
      $lines += ('    case ' + $value + ' = ''' + $value + ''';')
    }
  }
  $lines += '}'
  $fileName = $enum.Name + '.php'
  Set-Content -Path (Join-Path $root $fileName) -Value ($lines -join "`n")
}
