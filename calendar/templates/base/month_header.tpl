<!-- $Id: month_header.tpl,v 1.2 2006/12/08 19:55:56 sigurdne Exp $ -->
<!-- BEGIN monthly_header -->
 <tr colspan="{cols}" width="{col_width}%">
{column_header}</tr>
<!-- END monthly_header -->
<!-- BEGIN column_title -->
  <th width="11%" class="th"><font color="{font_color}">{col_title}</font></th>
<!-- END column_title -->
<!-- BEGIN month_column -->
  <td valign="top" height="75" colspan="1" width="{col_width}%"{extra}>
{column_data}
  </td>
<!-- END month_column -->

