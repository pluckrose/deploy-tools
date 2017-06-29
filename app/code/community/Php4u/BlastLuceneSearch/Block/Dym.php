<?php
/**
 * @category   Php4u
 * @package    Php4u_BlastLuceneSearch
 * @author     Marcin Szterling <marcin@php4u.co.uk>
 * @copyright  Php4u Marcin Szterling (c) 2013
 * @license http://php4u.co.uk/licence/
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * Any form of ditribution, sell, transfer, reverse engineering forbidden - see licence above
 *
 * Code was obfusacted due to previous licence violations
 */ 
$_F=__FILE__;$_X="JF9GPV9fRklMRV9fOyRfWD0iSkY5R1BWOWZSa2xNUlY5Zk95UmZXRDBpVEhsdmNVUlJiMmRMYVVKQldUSkdNRnBYWkhaamJtdG5TVU5DVVdGSVFUQmtVVEJMU1VOdloxRklRbWhaTW5Sb1dqSlZaMGxEUVdkVlIyaDNUa2hXWmxGdGVHaGpNMUpOWkZkT2JHSnRWbFJhVjBaNVdUSm5Ua05wUVhGSlJVSm9aRmhTYjJJelNXZEpRMEZuU1VVeGFHTnRUbkJpYVVKVVpXNVNiR050ZUhCaWJXTm5VRWN4YUdOdFRuQmlhMEozWVVoQk1HUlROV3BpZVRVeFlYbzBUa05wUVhGSlJVSnFZak5DTldOdGJHNWhTRkZuU1VaQ2IyTkVVakZKUlRGb1kyMU9jR0pwUWxSbGJsSnNZMjE0Y0dKdFkyZExSMDF3U1VSSmQwMVVSVTVEYVVGeFNVVkNjMkZYVG14aWJrNXNTVWRvTUdSSVFUWk1lVGwzWVVoQk1HUlROV3BpZVRVeFlYazVjMkZYVG14aWJVNXNUSGN3UzBsRGIyZFdSV2hHU1VaT1VGSnNVbGhSVmtwR1NVVnNWRWxHUWxOVU1WcEtVa1ZXUlVsRFNrSlZlVUpLVlhsSmMwbEdaRXBXUldoUVZsWlJaMVl3UmxOVmEwWlBWa1pyWjFRd1dXZFJWVFZhU1VWMFNsUnJVWE5KUlZaWlZVWktSbFV4VFdkVU1VbE9RMmxCY1VsRmJFNVZSWGhLVWxWUmMwbEZiRTlSTUhoV1VrVnNUMUo1UWtOV1ZsRm5WR3M1VlVsRmVFcFVWV3hWVWxWUloxWkZPR2RXUldoR1NVWmtRbFZzU2tKVWJGSktVbFpOWjFRd1dXZFVWVlpUVVRCb1FsUnNVa0pSYTJ4TlUxWlNXa3hCTUV0SlEyOW5VbXRzVlZSclZsUlZlVUpIVkRGSloxRlRRbEZSVmtwVlUxVk9WbFJGUmxOSlJrSldWV3hDVUZVd1ZXZFJWVFZGU1VVMVVGUnJiRTlTYkVwS1ZHdGtSbFJWVms5V1F6Um5VMVUwWjFSck9HZFNWbHBHVkd4UloxVXdhRUpVUlhkblZrVm9Sa1JSYjJkTGFVSkNWbFpTU1ZReFNsUkpSVGxUU1VWT1VGVkdiRk5UVldSSlZrTkNTVlF3ZUVWU1ZrcFVTVVZLUmtsRmVFcFJWVXBOVWxOQ1IxUXhTV2RSVlRWYVNVVk9UVkZWYkU1TVEwSkZVVlV4UWxJd1ZsUkpSVGxUU1VVNVZWTkZWbE5FVVc5blMybENUVk5WUmtOVFZYaEtWa1pyYzBsR1pFbFNWbEpKVWxaSloxTlZOR2RSVlRSblVWVk9WVk5WT1U5SlJUbEhTVVZPVUZSc1VsTlJWVTVWVEVOQ1ZWUXhTbFZKUlRsVFNVVTVWVk5GVmxOV01HeFVVbE4zWjFGV1NrcFZNR3hQVW5sQ1IxVnJPVTVNUVRCTFNVTnZaMVF4VmxWSlJUbEhTVVU1VTBsRmJFOUpSVTVRVkdzMVJsRXhVa3BVTURSblZqQnNWVk5EUWxWVFJWVm5WVEE1UjFaR1pFSlZhMVZuVkRGSloxWkZhRVpKUmxaVVVsTkNVRlZwUWxCV1JXaEdWV2xDUlZKVlJrMVRWVFZJVlhsQ1NsUm5NRXRKUTI5blZrVm9Sa2xHVGxCU2JGSllVVlpLUmt4bk1FdEpRMjluVVZjMU5VbEhXblpqYlRCbllqSlpaMXBIYkRCamJXeHBaRmhTY0dJeU5ITkpTRTVzWWtkM2MwbElVbmxaVnpWNldtMVdlVWxIV25aamJVcHdXa2RTYkdKcGQyZGpiVll5V2xoS2VscFRRbXhpYldSd1ltMVdiR050YkhWYWVVSnRZak5LYVdGWFVtdGFWelJuVEZOQ2VscFhWV2RpUjJ4cVdsYzFhbHBUUW1oWmJUa3lXbEV3UzBsRGIwNURhVUZ4U1VWT2RscEhWV2RrTWtaNlNVYzVhVnB1Vm5wWlYwNHdXbGRSWjFwSVZteEpTRkoyU1VoQ2VWcFlXbkJpTTFaNlNVZDRjRmt5Vm5WWk1sVm5aRzFzZG1KSFJqQmhWemwxWTNjd1MwbERiM1pFVVc5S1ExRnJaMWt5ZUdoak0wMW5WVWRvZDA1SVZtWlJiWGhvWXpOU1RXUlhUbXhpYlZaVVdsZEdlVmt5YUdaUmJYaDJXVEowWmxKSWJIUkpSMVkwWkVkV2RWcElUV2RVVjBadVdsWTVSRmxZVW1oaVJ6bHVWVEpXYUdOdFRtOVlNRXB6WWpKT2NsZ3hTbXhqTTFaelpFTkNOMGxJUW5saU0xSnNXVE5TYkZwRFFtMWtWelZxWkVkc2RtSnBRbVpaTWpsMVl6TlNlV1JYVGpCTFEydG5aWGxDZDFsWVNteGlibEUyVDJ3NWFtSXlOWHBrU0VveFdUTlJiMHRVYzJkS1NGSnZZVmhOZEZCdFJtdGFSVkpvWkVkRmIxbFlTbmxaV0d0dlNVTmthbGxYVG05YVZqbHpZVmRhYkdSSGJIUmFVMk5uVUZRMFowMTZXWGROUTNkblNqSk9hRmt5YUd4WU0xSm9Xak5OYmtsRU1DdEpSMFo1WTIxR05VdEdRbTlqUkZJeFdEQktjMWxZVGpCVVNGWnFXbGMxYkZVeVZtaGpiVTV2V0RBeGRscEhWbk5ZTUZJMVlsUnZObEV3UmtSVFJWWm1Wa1ZHU0V0VGQyZEtNazVvV1RKb2JGZ3lkR3hsVTJOblVGUTBaMHBJVW05aFdFMTBVR3c1YmxwWE5VUlpWMDV2V2xWMGJHVlRaM0JKUTJ0d1QzbENPVWxJUW5saU0xSnNXVE5TYkZwRFFtMWtWelZxWkVkc2RtSnBRbVphTWxaMVVUSkdhbUZIVmt4YVdHdHZTMU5DTjBsRFVtWlpWR041VFVSa2FrMTZUVE5hYW1jeFRXMVNhazVIU1ROT2Fra3dUMVJqZVU1NlNYaGFSMFpvVGxSUloxQlRRV2xqUjJoM1RraFdabUpJVm1wYVZ6VnNXREpTTldKVFNYVkpSekZyVGxObmEyUkhhSEJqZVRBcldqSldNRlV5Vm1oamJVNXZWVmhXYkdOdWEyOUxVelZPV1Zka2JFOXFjRzVhV0ZKVVpFYzVlVnBWVG5aaWJWcHdXakJhYzFsWFkyOUtNMEp2WTBSU01Vd3lValZpVXpsc1ltMUdhV0pIVm10S2VXdHdUM2xDZVZwWVVqRmpiVFJuU2tZNWFFNTZTWGRPTWsxNlRYcGtiVTlFVlhsYVIwMHdXV3BqTWsxcVVUVk9la2t6VFdwR2ExbFhSVEZPUkhOblpsTkNkMlJYU25OaFYwMW5XbTVXZFZrelVuQmlNalJuV2pKV01GVXlWbWhqYlU1dlZWaFdiR051YTI5TFUwSTNTVWhLYkdSSVZubGlhVUpPV1Zka2JFOXFjRzlhVjNoM1dsaEpiMG95VG1oa1IwWnpZakprZWxwWFJubFpNbWR1UzFNd0sxb3lWakJWV0Zac1kyNXNWVnBZYURCTFEyczNTVWd3WjJOSVZtbGlSMnhxU1VkYU1XSnRUakJoVnpsMVNVZGtiR1JGVWpWaVZrNHdZMjFzZFZwNVozQkpTSE5uU2tZNWFFNVhUbXBaZWxFelRucGFiVTF0VVhoWmVra3pXbFJzYlU5WFdUVk5NazEzVDFkSk5FMTZUVFZOUTBFNVNVVXhhRm95VlRaUGJXUnNaRVV4ZGxwSFZuTkxRMlJwWWtkR2VtUkhlREZaTWxaMVdsaE9iRmxZU21waFF6bHJaVmN3Ymt0VE1DdGpNbFl3VlROU2RtTnRWa3BhUTJoT1dWZGtiRTlxY0doalNFRnZTMU13SzFveVZqQlZNMUoyWTIxVmIwdFRNQ3RhTWxZd1UxZFJiMHRUYXpkSlExSm1XWHBzYWxsNlFtdE5SMVYzV1hwVmVVMHlTbTFOVkU1cFRtcHNhMXBIVW14UFJFMTVUMFJTYUU1WFJXZFFVMEZyV0RKRk1Wa3lUbXBPUkdNelRtMVplVnBFUm1wTmFtUnNUMWRaTlZwcWEzcFpla0UxV1dwbmVrMTZhM2RNVkRWdVdsaFNWR1JYWkc1YVdFNHdZVmM1ZFdONVoydGtSMmh3WTNrd0sxb3lWakJWTWxab1kyMU9iMVZZVm14amJtdHZTMU5yTjBsSGJHMUpRMmRyV0RKTk5Wa3lUWGRhUkVKc1RVZE5NVTFxVG1sYWFrVjZXV3BaTlZwSFVtdGFWR2Q2VFdwbk1GbFVWbWhMVTBJM1NVaEtiR1JJVm5saWFVRnJaRWRvY0dONU1DdGhSMVp6WTBkV2VVdERaSGRoU0VFd1pGTmpjRXhVTldaWWVXZHVVa2M0WjJWWE9URkpSekZzV1ZjMGJrdFROR2RLZVVGcFVFZEZaMkZJU214YWFqQnBTbmsxVGxsWFpHeFBhbkJ1V2xoU1ZtTnRkMjlLTWs1b1pFZEdjMkl5WkhwYVYwWjVXVEpuZG1OdFZucGtWM2d3U25sM1oxbFlTbmxaV0d0dlNqRTVlR1JYVm5sbFUyTm5VRlEwWjFsWVNubFpXR3R2U2pORmJrbEVNQ3RKUTFKbVdYcHNhbGw2UW10TlIxVjNXWHBWZVUweVNtMU5WRTVwVG1wc2ExcEhVbXhQUkUxNVQwUlNhRTVYUlhCTVEwRnVXRE5PYkZrelZubGFVMk5uVUZRMFoxUlhSbTVhVkc4MldWaENkMHREYTNSUWJXUnNaRVpPTUdJelNteExRMnQwVUcxc2VsRXpWbmxqYlZaMVpFZDROVlV5Vm1wa1dFcHNTME5yWjB0VFFYQk1hV05wVUdsamRVcElVbTloV0UxMFVHNVdlV0pGVm5wWk1rWjNXbE5uYTFneVRUVlpNazEzV2tSQ2JFMUhUVEZOYWs1cFdtcEZlbGxxV1RWYVIxSnJXbFJuZWsxcVp6QlpWRlpvUzFNMGJsQkRPV2hRYVVrdlNucHpaMlpUUW5sYVdGSXhZMjAwWjBwNVl6ZEpTREJuWTBoV2FXSkhiR3BKUjFveFltMU9NR0ZYT1hWSlIyeDZVa2hzZEZGWVdtaGhWM2hvV1cxNGJFdERhMmRsZVVKNVdsaFNNV050TkdkTFIwcDJZakozY0VsRFoydGtSMmh3WTNrd0sxb3lWakJTU0d4MFZUTlNlV0ZYTlc1TFEydG5TVlF3WjBwNVkzQlBlVUk1U1Vnd1BTSTdKRjlFUFhOMGNuSmxkaWduWldSdlkyVmtYelEyWlhOaFlpY3BPMlYyWVd3b0pGOUVLQ1JmV0NrcE93PT0iOyRfRD1zdHJyZXYoJ2Vkb2NlZF80NmVzYWInKTtldmFsKCRfRCgkX1gpKTs=";$_D=strrev('edoced_46esab');eval($_D($_X));