<div class="row listButton<?php echo $statusClass; ?>" id="<?php echo $id; ?>">
  <div class="row primary">
    <div class="buttonCell name col-xs-7 col-md-4">
      <span class="underage">
      <?php if ( $underAge ): ?>
        <i class='fa fa-child fa-lg'></i>
      <?php endif; ?>
      </span>
      <span class="icon"><i class="fa <?php echo $statusIcon; ?> fa-3x"></i></span>
      <span class="first"><?php echo $first; ?>&nbsp;</span>
      <span class="last"><?php echo $last; ?></span>
    </div>
    <div class="noClick buttonCell col-md-2 visible-md visible-lg">
      Order:&nbsp;
      <a href="<?php echo $orderLink; ?>" target="_blank">
        <span class="orderNum"><?php echo $orderNum; ?></span>
      </a>
    </div>
    <?php if ( $pickup ): ?>
    <div class="buttonCell col-xs-5 col-md-3 flexPickup<?php echo $pickupVisible; ?>">
      <?php echo $pickupName; ?>
    </div>
  <?php endif; ?>
    <div class="buttonCell col-xs-5 col-md-3 flexPackage<?php echo $packageVisible; ?>">
      <?php echo $package; ?>
    </div>
  </div>
  <div class="expanded" style="display: none;">
    <div class="row">
      <div class="buttonCell col-xs-5 col-md-6">
        <strong>Package:</strong><?php echo $package; ?>
      </div>
      <?php if ( $pickup ): ?>
      <div class="buttonCell col-xs-12 col-md-6">
        <strong>Pickup:</strong><?php echo $pickupName; ?>
      </div>
    <?php endif; ?>
    </div>
    <div class="row">
      <div class="buttonCell col-xs-12 col-md-6">
        <strong>Order:</strong>
        <a href="<?php echo $orderLink; ?>">
          <?php echo $orderNum; ?>
        </a>
      </div>
      <div class="buttonCell col-xs-12 col-md-6">
        <strong>Phone: </strong>
        <a href="tel:<?php echo $phone; ?>">
          <span class="phone"><?php echo $phone; ?></span>
        </a>
      </div>
    </div>
    <div class="row">
      <div class="buttonCell col-xs-12 col-md-6">
        <strong>Email: </strong>
        <a href="mailto:<?php echo $email; ?>">
          <span class="email"><?php echo $email; ?></span>
        </a>
      </div>
    </div>
    <div class="row">
      <br />
      <div class="buttonCell col-xs-4">
        <button class="btn btn-info" id="<?php echo $id; ?>:Reset">
          Reset
        </button>
      </div>
      <div class="buttonCell col-xs-4">
        <button class="btn btn-warning" id="<?php echo $id; ?>:NoShow">
          No Show
        </button>
      </div>
      <?php if ( $walkOn ): ?>
        <div class="buttonCell col-xs-4">
            <button class="btn btn-danger" id="<?php echo $id; ?>:Delete">
                Remove Order
            </button>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
