<?php

/**
 *  分页工具条类
 */
class PageTool
{

    /**
     * 得到分页工具条的html
     * @param $url   分页链接的url
     * @param $count   总条数
     * @param $page    当前页码
     * @param $pageSize   每个多少条
     * @return string
     */
    public static function show($url,$count,$page,$pageSize){
        //>>1. 根据总条数和每页多少条计算出总页数
        $total_page = ceil($count/$pageSize);

        //>>2.计算出上一页
        $pre_page = $page==1?1:$page-1;
        //>>3.计算出下一页
        $next_page = $page==$total_page?$total_page:$page+1;


        $pageHtml =  <<<XXXX
   <table id="page-table" cellspacing="0">
		<tbody>
			<tr>
				<td align="right" nowrap="true" style="background-color: rgb(255, 255, 255);">
					<div id="turn-page">
						总计  <span id="totalRecords">{$count}</span>个记录分为 <span id="totalPages">{$total_page}</span>页当前第<span id="pageCurrent">{$page}</span>
						页，每页 {$pageSize}	条	<span id="page-link">
							<a href="{$url}&page=1">第一页</a>
							<a href="{$url}&page={$pre_page}">上一页</a>
							<a href="{$url}&page={$next_page}">下一页</a>
							<a href="{$url}&page={$total_page}">最末页</a>
						</span>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
XXXX;
        return  $pageHtml;
    }
}