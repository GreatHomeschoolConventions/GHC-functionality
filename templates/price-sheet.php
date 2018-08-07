<?php
/**
 * Pricing sheet template
 *
 * @author AndrewRMinion Design
 * @package GHC_Functionality_Plugin
 */

$convention = $this->get_single_convention_info( $this_convention );
?>
<div class="container">
<table class="price-sheet <?php echo esc_attr( $convention['slug'] ); ?>">
	<caption><h2><?php echo esc_attr( $convention['convention_short_name'] ); ?> Homeschool Convention</h2></caption>
	<thead>
		<th scope="col">Benefits</th>
		<th scope="col">Free Shopping<br/><span class="smaller">(Thursday Night Only)</span></th>
		<th scope="col" colspan="2">Shopping Only</th>
		<th scope="col" colspan="2">Full Registration</th>
	</thead>
	<tbody>
		<?php
		// Benefits.
		foreach ( get_field( 'price_sheet_line_items', $convention['ID'] ) as $line_item ) {
			$included_free_shopping = $line_item['included_with_free_shopping'];
			$included_shopping      = $line_item['included_with_shopping_only'];
			$included_full          = $line_item['included_with_full_registration'];

			$find    = array( 'included', 'excluded', 'available' );
			$replace = array( '✓', '×', 'Addon' );

			$included_free_shopping_text = str_replace( $find, $replace, $included_free_shopping );
			$included_shopping_text      = str_replace( $find, $replace, $included_shopping );
			$included_full_text          = str_replace( $find, $replace, $included_full );

			?>
				<tr>
					<th aria-label="Benefits" scope="col">
						<dl>
							<dt><?php echo wp_kses_post( $line_item['price_sheet_item_title'] ); ?></dt>
							<dd><?php echo wp_kses_post( $line_item['price_sheet_item_details'] ); ?></dd>
						</dl>
					</th>
					<td aria-label="Free Shopping (Thursday Night Only)" class="<?php echo esc_attr( $included_free_shopping ); ?>"><?php echo esc_attr( $included_free_shopping_text ); ?></td>
					<td aria-label="Shopping Only" colspan="2" class="<?php echo esc_attr( $included_shopping ); ?>"><?php echo esc_attr( $included_shopping_text ); ?></td>
					<td aria-label="Full Registration" colspan="2" class="<?php echo esc_attr( $included_full ); ?>"><?php echo esc_attr( $included_full_text ); ?></td>
				</tr>
			<?php
		}
		?>
	</tbody>
	<tfoot>
		<?php
		// Prices.
		foreach ( get_field( 'pricing', $convention['ID'] ) as $price_item ) {
			?>
			<tr>
				<th aria-label="Dates" scope="col">
					<dl>
						<dt>Standard Registration</dt>
						<dd><?php echo wp_kses_post( $this->format_date_range( $price_item['begin_date'], $price_item['end_date'], 'Ymd' ) ); ?></dd>
					</dl>
				</th>
				<td aria-label="Free Shopping" class="price unavailable"></td>
				<td aria-label="Shopping Only" class="price unavailable" colspan="2"></td>
				<td aria-label="Full Registration" class="price individual">$<?php echo esc_attr( $price_item['individual_price'] ); ?> individual<a href="#footnotes">**</a></td>
				<td aria-label="Full Registration" class="price family">$<?php echo esc_attr( $price_item['family_price'] ); ?> family<a href="#footnotes">***</a></td>
			</tr>

			<?php
		}

		// At-the-door row.
		?>
		<tr>
			<th aria-label="Dates" scope="col">
				<dl>
					<dt><?php echo esc_attr( get_field( 'price_sheet_at_the_door_title', $convention['ID'] ) ); ?></dt>
					<dd><?php echo esc_attr( get_field( 'price_sheet_at_the_door_subtitle', $convention['ID'] ) ); ?></dd>
				</dl>
			</th>
			<td aria-label="Free Shopping" class="price door"><?php echo esc_attr( get_field( 'price_sheet_at_the_door_free_shopping', $convention['ID'] ) ); ?></td>
			<td aria-label="Shopping Only" class="price individual"><?php echo esc_attr( get_field( 'price_sheet_at_the_door_shopping_only_individual', $convention['ID'] ) ); ?></td>
			<td aria-label="Shopping Only" class="price family"><?php echo esc_attr( get_field( 'price_sheet_at_the_door_shopping_only_family', $convention['ID'] ) ); ?></td>
			<td aria-label="Full Registration" class="price individual"><?php echo esc_attr( get_field( 'price_sheet_at_the_door_full_convention_individual', $convention['ID'] ) ); ?></td>
			<td aria-label="Full Registration" class="price family"><?php echo esc_attr( get_field( 'price_sheet_at_the_door_full_convention_family', $convention['ID'] ) ); ?></td>
		</tr>

	</tfoot>
</table>

<ol id="footnotes">
	<li class="addon"><a href="#return">*</a>Additional tickets required.</li>
	<li class="individual"><a href="#return">**</a>Individual access includes children ages 4 and under.</li>
	<li class="family"><a href="#return">***</a>Family access includes spouse, children/teens, and grandparents.</li>
</ol>
</div>
